<?php

namespace Yahvya\EagleFramework\Database\Default\System;

use Exception;
use Override;
use PDO;
use PDOStatement;
use ReflectionClass;
use Yahvya\EagleFramework\Config\Config;
use Yahvya\EagleFramework\Config\ConfigException;
use Yahvya\EagleFramework\Config\DatabaseConfig;
use Yahvya\EagleFramework\Config\EnvConfig;
use Yahvya\EagleFramework\Database\Default\Attributes\EnumColumn;
use Yahvya\EagleFramework\Database\Default\Attributes\JoinedColumn;
use Yahvya\EagleFramework\Database\Default\Attributes\TableColumn;
use Yahvya\EagleFramework\Database\Default\Attributes\TableName;
use Yahvya\EagleFramework\Database\Default\Conditions\MysqlCondException;
use Yahvya\EagleFramework\Database\Default\CustomDatatypes\JoinedList;
use Yahvya\EagleFramework\Database\Default\Formatters\FormaterException;
use Yahvya\EagleFramework\Database\Default\QueryBuilder\MysqlQueryBuilder;
use Yahvya\EagleFramework\Database\System\DatabaseActionException;
use Yahvya\EagleFramework\Database\System\QueryCondition;
use Yahvya\EagleFramework\Database\System\QueryCondSeparator;
use Yahvya\EagleFramework\Database\System\DatabaseModel;
use Yahvya\EagleFramework\Routing\Application\Application;
use Yahvya\EagleFramework\Utils\List\EagleList;
use Throwable;

/**
 * @brief Mysql database model class base
 * @attention Make sure the sub attributes are protected|public
 */
abstract class MysqlModel extends DatabaseModel
{
    /**
     * @var TableName Table name provider
     */
    protected(set) TableName $tableNameProvider;

    /**
     * @var array{string:TableColumn} Database columns configurations provider. Configuration des colonnes de la base de donnée. Indexed by the column name
     */
    protected(set) array $dbColumnsConfig;

    /**
     * @var JoinedColumn[] Foreign columns configurations
     */
    protected(set) array $joinedColumnsConfig;

    /**
     * @var array Attributes original values without any formatting
     */
    public array $attributesOriginalValues = [];

    /**
     * @var MysqlQueryBuilder Internal query builder
     * @attention Be aware of the direct usage of the builder. Prefer to use an already defined method if you can and the 'prepareForNewQuery' method.
     */
    protected(set) MysqlQueryBuilder $queryBuilder;

    /**
     * @throws ConfigException On model configuration error
     */
    public function __construct()
    {
        $this->loadConfiguration();
        $this->queryBuilder = new MysqlQueryBuilder(baseModel: $this);
    }

    #[Override]
    public function create(): bool
    {
        try
        {
            $this->beforeCreate();

            $insertConfig = [];
            $columnsConfig = $this->dbColumnsConfig;
            $reflection = new ReflectionClass(objectOrClass: $this);

            foreach ($columnsConfig as $attributeName => $columnConfig)
            {
                if (!$reflection->getProperty(name: $attributeName)->isInitialized(object: $this))
                {
                    if ($columnConfig->isNullable())
                        $insertConfig[$attributeName] = null;
                }
                else
                    $insertConfig[$attributeName] = $columnConfig->convertFromValue(data: $this->$attributeName);
            }

            $statement = self::execQuery(queryBuilder: $this->prepareForNewQuery()->insert(insertConfig: $insertConfig));

            if ($statement === null)
                return false;

            $this->afterCreate();

            return true;
        }
        catch (Throwable)
        {
            return false;
        }
    }

    /**
     * @inheritDoc
     * @throws MysqlException On error
     */
    #[Override]
    public function update(): bool
    {
        $this->beforeUpdate();

        $updateConfig = [];

        foreach ($this->dbColumnsConfig as $attributeName => $columnConfig)
            $updateConfig[$attributeName] = $columnConfig->convertFromValue(data: $this->$attributeName);

        $queryBuilder = $this->prepareForNewQuery()->update(updateConfig: $updateConfig);

        $statement = self::execQuery(
            queryBuilder: self::buildPrimaryKeysCondOn(model: $this, queryBuilder: $queryBuilder)
        );

        if ($statement === null)
            return false;

        $this->beforeDelete();

        return true;
    }

    /**
     * @inheritDoc
     * @throws MysqlException On error
     */
    #[Override]
    public function delete(): bool
    {
        $this->beforeDelete();

        $queryBuilder = $this->prepareForNewQuery()->delete();

        // exécution de la requête
        $statement = self::execQuery(
            queryBuilder: self::buildPrimaryKeysCondOn(model: $this, queryBuilder: $queryBuilder)
        );

        if ($statement === null)
            return false;

        $this->afterDelete();

        return true;
    }

    /**
     * @brief Update an attribute value
     * @param string $attributeName Attribute to update name
     * @param mixed $value New value
     * @return $this
     * @throws ConfigException On error
     * @throws FormaterException On error
     * @throws MysqlCondException On error
     */
    public function setAttribute(string $attributeName, mixed $value): MysqlModel
    {
        $columnConfig = $this->dbColumnsConfig[$attributeName] ?? null;

        if ($columnConfig === null)
            throw new ConfigException(message: "Attribute not found");

        $formatedData = $columnConfig
            ->verifyData(baseModel: $this, attributeName: $attributeName, data: $value)
            ->formatData(baseModel: $this, originalData: $value);

        $this->attributesOriginalValues[$attributeName] = $value;
        $this->$attributeName = $formatedData;

        return $this;
    }

    /**
     * @brief Provided an attribute value
     * @param string $attributeName Attribute name
     * @param bool $reform If true, apply all associated re-formers with the attribute
     * @return mixed Attribute value or null
     * @throws ConfigException On error
     * @throws FormaterException On error
     */
    public function getAttribute(string $attributeName, bool $reform = true): mixed
    {
        $columnConfig = $this->dbColumnsConfig[$attributeName] ?? null;

        if ($columnConfig === null)
            throw new ConfigException(message: "Attribut non trouvé");

        $data = $this->$attributeName;

        if ($reform)
            $data = $columnConfig->reformData(baseModel: $this, formatedData: $data);

        return $data;
    }

    /**
     * @brief Provide the original value of the attribute
     * @attention If the value was inserted in the database, the original value equals the version stored in the database
     * @param string $attributeName Attribute name
     * @return mixed The original value or null
     */
    public function getAttributOriginal(string $attributeName): mixed
    {
        return $this->attributesOriginalValues[$attributeName] ?? null;
    }

    /**
     * @brief Provide the column configuration of an attribute
     * @param string $attributName Attribute name
     * @return TableColumn|EnumColumn|null Column configuration name
     */
    public function getColumnConfig(string $attributName): TableColumn|EnumColumn|null
    {
        return $this->dbColumnsConfig[$attributName] ?? null;
    }

    /**
     * @brief Transform model data into an associative array
     * @param bool $addJoinedColumns If true, add the joined columns values
     * @return array{string:mixed} Model data as an associative array
     * @throws Exception On error
     */
    public function getAsArray(bool $addJoinedColumns = true): array
    {
        $result = [];

        foreach ($this->dbColumnsConfig as $attributeName => $_)
            $result[$attributeName] = $this->getAttribute(attributeName: $attributeName);

        if (!$addJoinedColumns)
            return $result;

        foreach ($this->joinedColumnsConfig as $attributeName => $_)
            $result[$attributeName] = array_map(
                callback: fn(MysqlModel $joinedModel): array => $joinedModel->getAsArray(),
                array: $this->$attributeName->toArray()
            );

        return $result;
    }

    #[Override]
    public function afterGeneration(mixed $datas = []): DatabaseModel
    {
        parent::afterGeneration(datas: $datas);

        foreach ($this->dbColumnsConfig as $attributeName => $_)
            $this->attributesOriginalValues[$attributeName] = $this->$attributeName;

        return $this;
    }

    #[Override]
    protected function beforeCreate(mixed $datas = []): DatabaseModel
    {
        return parent::beforeCreate(datas: $datas);
    }

    #[Override]
    protected function afterCreate(mixed $datas = []): DatabaseModel
    {
        return parent::afterCreate(datas: $datas);
    }

    #[Override]
    protected function afterUpdate(mixed $datas = []): DatabaseModel
    {
        return parent::afterUpdate(datas: $datas);
    }

    #[Override]
    protected function beforeUpdate(mixed $datas = []): DatabaseModel
    {
        return parent::beforeUpdate(datas: $datas);
    }

    #[Override]
    protected function afterDelete(mixed $datas = []): DatabaseModel
    {
        return parent::afterDelete(datas: $datas);
    }

    #[Override]
    protected function beforeDelete(mixed $datas = []): DatabaseModel
    {
        return parent::beforeDelete(datas: $datas);
    }

    /**
     * @inheritDoc
     * @throws ConfigException On error
     */
    #[Override]
    protected function beforeGeneration(mixed $datas = []): MysqlModel
    {
        return parent::beforeGeneration(datas: $datas);
    }

    /**
     * @brief Load the model configuration
     * @return void
     * @throws ConfigException In case of a bad configuration
     */
    protected function loadConfiguration(): void
    {
        $reflection = new ReflectionClass(objectOrClass: $this);

        $found = false;

        foreach ($reflection->getAttributes() as $attribute)
        {
            if ($attribute->getName() === TableName::class)
            {
                $this->tableNameProvider = $attribute->newInstance();
                $found = true;
                break;
            }
        }

        if (!$found)
            throw new ConfigException(message: "Badly configured model");

        $this->dbColumnsConfig = [];
        $this->joinedColumnsConfig = [];

        foreach ($reflection->getProperties() as $property)
        {
            $propertyName = $property->getName();

            // Search the description attribute
            foreach ($property->getAttributes() as $attribute)
            {
                $instance = $attribute->newInstance();

                if ($instance instanceof TableColumn)
                {
                    $this->dbColumnsConfig[$propertyName] = $instance;
                    break;
                }

                if ($instance instanceof JoinedColumn)
                {
                    $this->joinedColumnsConfig[$propertyName] = $instance;
                    break;
                }
            }
        }
    }

    /**
     * @brief Prepare the internal query builder for a new request
     * @return MysqlQueryBuilder The query builder ready for a new request
     */
    protected function prepareForNewQuery(): MysqlQueryBuilder
    {
        return $this->queryBuilder->reset();
    }

    /**
     * @return int|null The last insert id
     * @throws ConfigException On error
     */
    protected function lastInsertId(): int|null
    {
        $provider = self::getDatabaseConfig()->getConfig(name: DatabaseConfig::PROVIDER->value);

        return $provider->getCon()?->lastInsertId();
    }

    /**
     * @brief Generate a model from a fetched line
     * @param PDOStatement|null $statement PDO statement
     * @param MysqlQueryBuilder $queryBuilder Query builder instance
     * @return MysqlModel|null Generated model
     * @throws MysqlException On error
     * @throws ConfigException On error
     * @throws DatabaseActionException On error
     */
    public static function createFromDatabaseLine(?PDOStatement $statement, MysqlQueryBuilder $queryBuilder): MysqlModel|null
    {
        if ($statement === null)
            throw new MysqlException(message: "Fail to build the request");

        $lineConfig = $statement->fetch(mode: PDO::FETCH_ASSOC);

        if ($lineConfig === null || $lineConfig === false)
            return null;

        return self::createModelFromLine(
            line: $lineConfig,
            modelClass: get_class(object: $queryBuilder->baseModel)
        );
    }

    /**
     * @brief Generate a model from the fetched lines
     * @param PDOStatement|null $statement PDO statement
     * @param MysqlQueryBuilder $queryBuilder Query builder instance
     * @return EagleList Generated model list
     * @throws MysqlException On error
     * @throws ConfigException On error
     * @throws DatabaseActionException On error
     */
    public static function createFromDatabaseLines(?PDOStatement $statement, MysqlQueryBuilder $queryBuilder): EagleList
    {
        $models = [];

        while (true)
        {
            $model = self::createFromDatabaseLine(statement: $statement, queryBuilder: $queryBuilder);

            if ($model === null)
                break;

            $models[] = $model;
        }

        return new EagleList(datas: $models);
    }

    /**
     * @brief Get the first which match the conditions
     * @param MysqlCondition|MysqlCondSeparator ...$findBuilders Request parts
     * @return MysqlModel|null Founded row as model or null
     * @throws ConfigException On error
     */
    #[Override]
    public static function findOne(QueryCondition|QueryCondSeparator ...$findBuilders): MysqlModel|null
    {
        try
        {
            $queryBuilder = MysqlQueryBuilder::createFrom(modelClass: get_called_class());

            $queryBuilder->select();

            if (!empty($findBuilders))
                $queryBuilder->where()->cond(...$findBuilders);

            $queryBuilder->limit(count: 1);

            return self::createFromDatabaseLine(statement: self::execQuery(queryBuilder: $queryBuilder), queryBuilder: $queryBuilder);
        }
        catch (ConfigException $e)
        {
            throw $e;
        }
        catch (Throwable)
        {
            return null;
        }
    }

    /**
     * @brief Find all rows that match the conditions
     * @param QueryCondition|QueryCondSeparator ...$findBuilders Query parts
     * @return EagleList<MysqlModel> Founded rows as a model list
     * @throws ConfigException On error
     * @throws MysqlException On error
     * @throws DatabaseActionException On error
     */
    #[Override]
    public static function findAll(QueryCondition|QueryCondSeparator ...$findBuilders): EagleList
    {
        $queryBuilder = MysqlQueryBuilder::createFrom(modelClass: get_called_class());

        $queryBuilder->select();

        if (!empty($findBuilders))
            $queryBuilder->where()->cond(...$findBuilders);

        return self::createFromDatabaseLines(statement: self::execQuery(queryBuilder: $queryBuilder), queryBuilder: $queryBuilder);
    }

    /**
     * @param string $modelClass Model instance
     * @return MysqlModel Model instance
     * @throws ConfigException On error
     */
    public static function newInstanceOfModel(string $modelClass): MysqlModel
    {
        try
        {
            $reflection = new ReflectionClass(objectOrClass: $modelClass);

            $model = $reflection->newInstance();

            if (!($model instanceof MysqlModel))
                throw new ConfigException(message: "The provided class should be a child class of the <" . MysqlModel::class . "> class");

            return $model;
        }
        catch (ConfigException $e)
        {
            throw $e;
        }
        catch (Throwable)
        {
            throw new ConfigException(message: "An error occurred during the build of the model");
        }
    }

    /**
     * @brief Execute the request and provide the statement
     * @param MysqlQueryBuilder $queryBuilder Query builder instance
     * @param bool $execute If true, the query will be executed
     * @return PDOStatement|null The statement or null
     * @throws ConfigException On error
     */
    public static function execQuery(MysqlQueryBuilder $queryBuilder, bool $execute = true): ?PDOStatement
    {
        $provider = self::getDatabaseConfig()->getConfig(name: DatabaseConfig::PROVIDER->value);

        $statement = $queryBuilder->prepareRequest(pdo: $provider->getCon());

        if ($statement === null || ($execute && !$statement->execute()))
            return null;

        return $statement;
    }

    /**
     * @brief Load the provided joined column data
     * @param MysqlModel $model Model instance to load the data in
     * @param JoinedColumn $joinedColumn Join configuration
     * @return EagleList<MysqlModel> Load result
     * @throws MysqlException On error
     */
    public static function loadJoinedColumns(MysqlModel $model, JoinedColumn $joinedColumn): EagleList
    {
        $joinConfig = $joinedColumn->getJoinConfig();

        $conditions = [MysqlCondSeparator::GROUP_START()];

        foreach ($joinConfig as $baseModelAttributeName => $joinModelAttributeName)
        {
            $conditions[] = new MysqlCondition(
                condGetter: $joinModelAttributeName,
                comparator: MysqlComparator::EQUAL(),
                conditionValue: $model->$baseModelAttributeName
            );

            $conditions[] = MysqlCondSeparator::AND();
        }

        $size = count(value: $conditions);

        if ($size === 1)
            throw new MysqlException(message: "No matching condition on the joined list", isDisplayable: false);

        $conditions[$size - 1] = MysqlCondSeparator::GROUP_END();

        return @call_user_func_array([$joinedColumn->getClassModel(), "findAll"], $conditions);
    }

    /**
     * @brief Build a model from the configuration
     * @param array $line Database row content
     * @param string $modelClass Expected model class
     * @return MysqlModel The built model
     * @throws ConfigException On error
     * @throws MysqlException On error
     * @throws DatabaseActionException On error
     */
    public static function createModelFromLine(array $line, string $modelClass): MysqlModel
    {
        $model = self::newInstanceOfModel(modelClass: $modelClass);

        $columnsConfig = $model->dbColumnsConfig;

        $linkedValues = [];

        // Build of the reversed conditions array
        foreach ($line as $columnRealName => $dbValue)
        {
            foreach ($columnsConfig as $attributeName => $columnConfig)
            {
                if ($columnConfig->getColumnName() === $columnRealName)
                {
                    $linkedValues[$attributeName] = $dbValue;

                    break;
                }
            }
        }

        $model->beforeGeneration(datas: $linkedValues);

        foreach ($linkedValues as $attributeName => $dbValue)
            $model->$attributeName = $columnsConfig[$attributeName]->convertToValue(data: $dbValue);

        foreach ($model->joinedColumnsConfig as $attributeName => $config)
        {
            $list = new JoinedList(descriptor: $config, linkedModel: $model);
            $model->$attributeName = $list;

            if (!$config->getLoadOnGeneration())
                continue;

            $list->loadContent();
        }

        $model->afterGeneration();

        return $model;
    }

    /**
     * @brief Add to the query builder the check conditions of primary keys
     * @param MysqlModel $model Model instance
     * @param MysqlQueryBuilder $queryBuilder Query builder
     * @param bool $addWhere If true, add ->where() followed by the conditions. If false, add an 'AND' condition before adding the primary keys conditions group
     * @return MysqlQueryBuilder Modified builder
     * @throws MysqlException In the case of non-present primary keys
     */
    public static function buildPrimaryKeysCondOn(MysqlModel $model, MysqlQueryBuilder $queryBuilder, bool $addWhere = true): MysqlQueryBuilder
    {
        if ($addWhere)
            $queryBuilder->where();
        else
            $queryBuilder->cond(MysqlCondSeparator::AND());

        $columnsConfig = $model->dbColumnsConfig;
        $primaryKeysCond = [];

        foreach ($columnsConfig as $attributeName => $columnConfig)
        {
            if ($columnConfig->isPrimaryKey())
            {
                $primaryKeysCond[] = new MysqlCondition(
                    condGetter: $attributeName,
                    comparator: MysqlComparator::EQUAL(),
                    conditionValue: $columnConfig->convertFromValue(data: $model->$attributeName)
                );
                $primaryKeysCond[] = MysqlCondSeparator::AND();
            }
        }

        if (empty($primaryKeysCond))
            throw new MysqlException(message: "No primary key found", isDisplayable: false);

        // replace the last 'AND' condition
        $primaryKeysCond[count(value: $primaryKeysCond) - 1] = MysqlCondSeparator::GROUP_END();

        return $queryBuilder->cond(
            MysqlCondSeparator::GROUP_START(),
            ...$primaryKeysCond
        );
    }

    /**
     * @return Config Application database configuration
     * @throws ConfigException On error
     */
    protected static function getDatabaseConfig(): Config
    {
        return Application::getEnvConfig()->getConfig(name: EnvConfig::DATABASE_CONFIG->value);
    }
}
