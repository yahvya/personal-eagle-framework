<?php

namespace Yahvya\EagleFramework\Database\Default\QueryBuilder;

use PDO;
use PDOStatement;
use Yahvya\EagleFramework\Config\ConfigException;
use Yahvya\EagleFramework\Database\Default\Attributes\TableColumn;
use Yahvya\EagleFramework\Database\Default\System\MysqlCondition;
use Yahvya\EagleFramework\Database\Default\System\MysqlCondSeparator;
use Yahvya\EagleFramework\Database\Default\System\MysqlFunction;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;
use Yahvya\EagleFramework\Database\Default\System\MysqlBindDatas;
use Throwable;

/**
 * @brief Mysql query builder
 */
class MysqlQueryBuilder
{
    /**
     * @var string Query SQL string
     */
    protected(set) string $sqlString;

    /**
     * @var MysqlBindDatas[] Value to bind
     */
    protected(set) array $toBind;

    /**
     * @var string Table alias in the query
     */
    protected string $tableAlias;

    /**
     * @param $baseModel $model Model which the query builder should be based on
     */
    public function __construct(protected(set) MysqlModel $baseModel)
    {
        $this->reset();
    }

    /**
     * @briefCreate a query builder from the provided model class
     * @param string $modelClass Model class
     * @return MysqlQueryBuilder Generated query builder
     * @throws ConfigException On error
     */
    public static function createFrom(string $modelClass): MysqlQueryBuilder
    {
        return new MysqlQueryBuilder(baseModel: MysqlModel::newInstanceOfModel(modelClass: $modelClass));
    }

    /**
     * @brief Reset to 0 the query builder content
     * @return $this
     */
    public function reset(): MysqlQueryBuilder
    {
        $this->sqlString = "";
        $this->toBind = [];
        $this->tableAlias = $this->baseModel->tableNameProvider->tableName . time();

        return $this;
    }

    /**
     * @brief Prepare the build request
     * @param PDO $pdo PDO instance
     * @return PDOStatement|null PDO statement
     */
    public function prepareRequest(PDO $pdo): ?PDOStatement
    {
        try
        {
            $statement = $pdo->prepare(query: $this->getSql());

            if ($statement === false)
                return null;

            // add the values to bind
            $toBind = $this->toBind;
            $bindCounter = 0;

            foreach ($toBind as $bindManager)
            {
                foreach ($bindManager->dataToBind as $bindConfig)
                {
                    $bindCounter++;
                    $statement->bindValue($bindCounter, ...$bindConfig);
                }
            }

            return $statement;
        }
        catch (Throwable)
        {
            return null;
        }
    }

    /**
     * @brief Update the table-defined alias
     * @param string $alias New alias
     * @return $this
     */
    public function as(string $alias): MysqlQueryBuilder
    {
        $this->tableAlias = $alias;

        return $this;
    }

    /**
     * @return string The SQL string without any changes
     * @attention The provided string by this method could probably not be used as such
     */
    public function getRealSql(): string
    {
        return $this->sqlString;
    }

    /**
     * @return string The formated SQL string ready for a request
     */
    public function getSql(): string
    {
        return str_replace(
            search: ["{aliasTable}"],
            replace: [$this->tableAlias],
            subject: $this->sqlString
        );
    }

    /**
     * @brief Join the provided request with the actuel one
     * @param MysqlQueryBuilder $toJoin Query builder to join
     * @param string|null $sqlBefore SQL string to place before or null
     * @param string|null $sqlAfter SQL string to place after or null
     * @return $this
     */
    public function joinBuilder(MysqlQueryBuilder $toJoin, ?string $sqlBefore = null, ?string $sqlAfter = null): MysqlQueryBuilder
    {
        $this->sqlString .= ($sqlBefore ?? "") . $toJoin->getSql() . ($sqlAfter ?? "");
        $this->toBind = array_merge($this->toBind, $toJoin->toBind);

        return $this;
    }

    /**
     * @brief Get the values to bind based on the provided data and provide the resulting SQL string
     * @param TableColumn $columnConfig Column configuration
     * @param mixed|MysqlFunction|MysqlQueryBuilder $data To treat
     * @param string|null $sqlBefore SQL string to place before or null
     * @param string|null $sqlAfter SQL string to place after or null
     * @return array{string:mixed} Data with the next format: ["sql" => ...,"toBind" => [MysqlBindDatas, ...]
     * @attention Do not change the SQL string
     */
    protected function manageValueDatas(TableColumn $columnConfig, mixed $data, ?string $sqlBefore = null, ?string $sqlAfter = null): array
    {
        if ($data instanceof MysqlQueryBuilder)
        {
            return [
                "sql" => ($sqlBefore ?? "") . $data->getSql() . ($sqlAfter ?? ""),
                "toBind" => $data->toBind
            ];
        }
        else if ($data instanceof MysqlFunction)
        {
            $alias = $data->alias;
            $function = $data->function;

            if ($data->replaceAttributesName)
                $function = $this->replaceAttributesNameIn(string: $function);

            return [
                "sql" => $function . ($alias ? " AS $alias" : ""),
                "toBind" => []
            ];
        }
        else
        {
            $toBind = new MysqlBindDatas(
                countOfMarkers: 1,
                toBindDatas: [[$data, $data === null ? PDO::PARAM_NULL : $columnConfig->getColumnType()]]
            );

            return [
                "sql" => $toBind->getMarkersStr(),
                "toBind" => [$toBind]
            ];
        }
    }

    /**
     * @brief Replace attributes names by the associated column
     * @param string $string String to treat
     * @return string Result
     * @attention An attribute name should be place into braces {}
     */
    protected function replaceAttributesNameIn(string $string): string
    {
        $tableColumnsConfig = $this->baseModel->dbColumnsConfig;

        foreach ($tableColumnsConfig as $attributeNameToReplace => $attributeConfig)
            $string = @str_replace(search: "{{$attributeNameToReplace}}", replace: $attributeConfig->getColumnName(), subject: $string);

        return $string;
    }

    /**
     * @brief Parse the condition data
     * @param MysqlCondition $condition Condition
     * @return array Condition data ["sql" => ...,"toBind" => MysqlBindDatas]
     */
    protected function parseCondition(MysqlCondition $condition): array
    {
        $comparator = $condition->comparator;
        $condGetter = $condition->condGetter;

        if ($condGetter instanceof MysqlFunction)
        {
            $function = $condGetter->function;

            if ($condGetter->replaceAttributesName)
                $function = $this->replaceAttributesNameIn(string: $function);

            $sql = "$function ";
        }
        else
        {
            $sql = "{$this->baseModel->getColumnConfig(attributName: $condGetter)->columnName} ";
        }

        // treatment of the markers replacement
        $toBind = $comparator->getBindDatas(value: $condition->conditionValue);

        $comparatorStr = str_replace(
            search: ["{singleMarker}", "{bindMarkers}"],
            replace: ["?", $toBind->getMarkersStr()],
            subject: $comparator->comparator
        );

        return [
            "sql" => $sql . $comparatorStr,
            "toBind" => $toBind
        ];
    }

    /**
     * @briefParse a sequence of conditions and separators
     * @param (MysqlCondition|MysqlCondSeparator)[] $sequence Sequence
     * @return array Sequence data ["sql" => ...,"toBind" => [MysqlBindDatas, ...]]
     */
    protected function parseConditionSequence(array $sequence): array
    {
        $sql = "";
        $toBindList = [];

        foreach ($sequence as $conditionConfig)
        {
            if ($conditionConfig instanceof MysqlCondSeparator)
            {
                $sql .= "$conditionConfig->separator ";
                continue;
            }

            ["sql" => $parsedSql, "toBind" => $toBind] = $this->parseCondition($conditionConfig);

            $sql .= "$parsedSql ";
            $toBindList[] = $toBind;
        }

        return [
            "sql" => $sql,
            "toBind" => $toBindList
        ];
    }

    // REQUEST METHODS

    /**
     * @brief Start a static request
     * @param string $sqlString SQL query string
     * @param MysqlBindDatas[] $toBind values to bind
     * @param bool $justConcat If true, concat. If false, replace
     * @return $this
     */
    public function staticRequest(string $sqlString, array $toBind = [], bool $justConcat = false): MysqlQueryBuilder
    {
        if ($justConcat)
        {
            $this->sqlString .= $sqlString;
            $this->toBind = array_merge($this->toBind, $toBind);
        }
        else
        {
            $this->sqlString = $sqlString;
            $this->toBind = $toBind;
        }

        return $this;
    }

    /**
     * @brief Add SELECT [] FROM table string
     * @param string|MysqlFunction ...$toSelect
     * @return $this
     * @attention Depending on the selected fields, the generated models would be partially built
     */
    public function select(string|MysqlFunction ...$toSelect): MysqlQueryBuilder
    {
        $this->sqlString .= "SELECT ";

        $tableColumnsConfig = $this->baseModel->dbColumnsConfig;

        $columnsToSelect = [];

        foreach ($toSelect as $value)
        {
            if (gettype($value) === "string")
            {
                $columnsToSelect[] = $tableColumnsConfig[$value]->getColumnName();
                continue;
            }

            $alias = $value->alias;
            $function = $value->function;

            if ($value->replaceAttributesName)
                $function = $this->replaceAttributesNameIn(string: $function);

            $columnsToSelect[] = $function . ($alias ? " AS $alias" : "");
        }

        $this->sqlString .= (empty($columnsToSelect) ? "*" : implode(separator: ",", array: $columnsToSelect)) . " FROM {$this->baseModel->tableNameProvider->tableName} AS {aliasTable} ";

        return $this;
    }

    /**
     * @brief ADD the INSERT INTO string
     * @param array{string:MysqlFunction|MysqlQueryBuilder|mixed} $insertConfig Array indexed by the attribute name associated with the value
     * @return $this
     * @attention In case of a function, don't place an alias
     */
    public function insert(array $insertConfig): MysqlQueryBuilder
    {
        $this->sqlString .= "INSERT INTO {$this->baseModel->tableNameProvider->tableName} ";

        $columnsToInsert = [];
        $sql = [];
        $columnsConfig = $this->baseModel->dbColumnsConfig;

        foreach ($insertConfig as $attributeName => $value)
        {
            ["sql" => $setSql, "toBind" => $valuesToBind] = $this->manageValueDatas(
                columnConfig: $columnsConfig[$attributeName],
                data: $value,
                sqlBefore: "(",
                sqlAfter: ")"
            );

            $columnsToInsert[] = $columnsConfig[$attributeName]->getColumnName();
            $sql[] = "$setSql";

            $this->toBind = array_merge($this->toBind, $valuesToBind);
        }

        $this->sqlString .= "(" . implode(separator: ",", array: $columnsToInsert) . ") VALUES(" . implode(separator: ",", array: $sql) . ")";

        return $this;
    }

    /**
     * @brief ADD the UPDATE table SET [] string
     * @param array{string:MysqlFunction|MysqlQueryBuilder|mixed} $updateConfig Array indexed by the attribute name associated with the value
     * @return $this
     * @attention en cas de fonction ne pas y placer d'alias
     */
    public function update(array $updateConfig): MysqlQueryBuilder
    {
        $this->sqlString .= "UPDATE {$this->baseModel->tableNameProvider->tableName} AS {aliasTable} SET ";

        $columnsConfig = $this->baseModel->dbColumnsConfig;

        $sql = [];

        // ajout des attributs à modifier
        foreach ($updateConfig as $attributeName => $newValue)
        {
            ["sql" => $setSql, "toBind" => $valuesToBind] = $this->manageValueDatas(
                columnConfig: $columnsConfig[$attributeName],
                data: $newValue,
                sqlBefore: "(",
                sqlAfter: ")"
            );

            $sql[] = "{$columnsConfig[$attributeName]->getColumnName()} = $setSql";

            $this->toBind = array_merge($this->toBind, $valuesToBind);
        }

        $this->sqlString .= implode(separator: ", ", array: $sql) . " ";

        return $this;
    }

    /**
     * @brief ADD the DELETE FROM table string
     * @return $this
     */
    public function delete(): MysqlQueryBuilder
    {
        $this->sqlString .= "DELETE FROM {$this->baseModel->tableNameProvider->tableName} AS {aliasTable} ";

        return $this;
    }

    /**
     * @brief ADD the WHERE string
     * @return $this
     */
    public function where(): MysqlQueryBuilder
    {
        $this->sqlString .= "WHERE ";

        return $this;
    }

    /**
     * @brief Add request conditions
     * @param MysqlCondition|MysqlCondSeparator ...$conditions Conditions
     * @return $this
     */
    public function cond(MysqlCondition|MysqlCondSeparator ...$conditions): MysqlQueryBuilder
    {
        ["sql" => $sql, "toBind" => $toBind] = $this->parseConditionSequence(sequence: $conditions);

        $this->sqlString .= "$sql ";
        $this->toBind = array_merge($this->toBind, $toBind);

        return $this;
    }

    /**
     * @brief ADD the HAVING string
     * @param MysqlCondition|MysqlCondSeparator ...$conditions Conditions
     * @return $this
     */
    public function having(MysqlCondition|MysqlCondSeparator ...$conditions): MysqlQueryBuilder
    {
        ["sql" => $sql, "toBind" => $toBind] = $this->parseConditionSequence(sequence: $conditions);

        $this->sqlString .= "HAVING $sql ";
        $this->toBind = array_merge($this->toBind, $toBind);

        return $this;
    }

    /**
     * @brief ADD the ORDER BY string. Call ex: $builder->orderBy(["price","ASC"],["id","DESC"] )
     * @param array ...$configs Arrays of two elements containing in the first position, the attribute name followed by ASC or DESC as the second argument
     * @return $this
     */
    public function orderBy(array ...$configs): MysqlQueryBuilder
    {
        $this->sqlString .= "ORDER BY ";
        $sql = [];

        foreach ($configs as $orderConfig)
        {
            [$attributeName, $sortOrder] = $orderConfig;

            $sql[] = "{$this->baseModel->getColumnConfig(attributName: $attributeName)->columnName} $sortOrder";
        }

        $this->sqlString .= implode(separator: ",", array: $sql) . " ";

        return $this;
    }

    /**
     * @brief Ajoute la chaine GROUP BY
     * @param string ...$attributesNames nom des attributs
     * @return $this
     */
    public function groupBy(string ...$attributesNames): MysqlQueryBuilder
    {
        $this->sqlString .= "GROUP BY " . implode(
                separator: ",",
                array: array_map(
                    callback: fn(string $attributeName): string => $this->baseModel->getColumnConfig(attributName: $attributeName)->columnName,
                    array: $attributesNames
                )
            ) . " ";

        return $this;
    }

    /**
     * @brief ADD the LIMIT clause
     * @param int $count Count of elements
     * @param int|null $offset Offset
     * @return $this
     */
    public function limit(int $count, ?int $offset = null): MysqlQueryBuilder
    {
        if ($offset == null)
        {
            $this->sqlString .= "LIMIT ? ";
            $this->toBind[] = new MysqlBindDatas(
                countOfMarkers: 1,
                toBindDatas: [[$count, PDO::PARAM_INT]]
            );
        }
        else
        {
            $this->sqlString .= "LIMIT ? OFFSET ? ";
            $this->toBind[] = new MysqlBindDatas(
                countOfMarkers: 2,
                toBindDatas: [[$count, PDO::PARAM_INT], [$offset, PDO::PARAM_INT]]
            );
        }

        return $this;
    }
}