<?php

namespace SaboCore\Database\Default\System;

use Override;
use PDO;
use PDOStatement;
use ReflectionClass;
use SaboCore\Config\Config;
use SaboCore\Config\ConfigException;
use SaboCore\Config\DatabaseConfig;
use SaboCore\Config\EnvConfig;
use SaboCore\Database\Default\Attributes\EnumColumn;
use SaboCore\Database\Default\Attributes\JoinedColumn;
use SaboCore\Database\Default\Attributes\TableColumn;
use SaboCore\Database\Default\Attributes\TableName;
use SaboCore\Database\Default\Conditions\MysqlCondException;
use SaboCore\Database\Default\CustomDatatypes\JoinedList;
use SaboCore\Database\Default\Formatters\FormaterException;
use SaboCore\Database\Default\QueryBuilder\MysqlQueryBuilder;
use SaboCore\Database\System\DatabaseActionException;
use SaboCore\Database\System\DatabaseCondition;
use SaboCore\Database\System\DatabaseCondSeparator;
use SaboCore\Database\System\DatabaseModel;
use SaboCore\Routing\Application\Application;
use SaboCore\Utils\List\SaboList;
use Throwable;

/**
 * @brief Modèle de la base de données mysql
 * @author yahaya bathily https://github.com/yahvya
 * @attention les attributs utilisables doivent être protected|public
 */
class MysqlModel extends DatabaseModel{
    /**
     * @var TableName Fournisseur du nom de la table
     */
    protected TableName $tableName;

    /**
     * @var TableColumn[] Configuration des colonnes de la base de donnée. Indicé par le nom de l'attribut et contient comme valeur l'instance de TableColumn
     */
    protected array $dbColumnsConfig;

    /**
     * @var JoinedColumn[] Configuration des colonnes de jointures
     */
    protected array $joinedColumnsConfig;

    /**
     * @var array Valeur originale des attributs sans formatage
     */
    protected array $attributesOriginalValues = [];

    /**
     * @var MysqlQueryBuilder constructeur de requête interne
     * @attention À l'utilisation directe du QueryBuilder. Privilégiez de passer par la méthode "prepareForNewQuery"
     */
    protected MysqlQueryBuilder $queryBuilder;

    /**
     * @throws ConfigException en cas d'erreur de configuration du model
     */
    public function __construct(){
        $this->loadConfiguration();
        $this->queryBuilder = new MysqlQueryBuilder(model: $this);
    }

    #[Override]
    public function create(): bool{
        try{
            $this->beforeCreate();

            // construction des champs à insérer
            $insertConfig = [];
            $columnsConfig = $this->getColumnsConfig();
            $reflection = new ReflectionClass(objectOrClass: $this);

            foreach($columnsConfig as $attributeName => $columnConfig){
                if(!$reflection->getProperty(name: $attributeName)->isInitialized(object: $this)){
                    if($columnConfig->isNullable() )
                        $insertConfig[$attributeName] = null;
                }
                else
                    $insertConfig[$attributeName] = $columnConfig->convertFromValue(data: $this->$attributeName);
            }

            $statement = self::execQuery(queryBuilder: $this->prepareForNewQuery()->insert(insertConfig: $insertConfig));

            if($statement === null)
                return false;

            $this->afterCreate();

            return true;
        }
        catch(Throwable){
            return false;
        }
    }

    /**
     * @brief Met à jour la ligne en base de données, basé sur les clés primaires
     * @return bool si la mise à jour s'est produite
     * @throws ConfigException en cas d'erreur
     * @throws DatabaseActionException en cas d'erreur
     * @throws MysqlException en cas de clé primaire non fourni
     */
    #[Override]
    public function update(): bool{
        $this->beforeUpdate();

        $updateConfig = [];

        // récupération des valeurs actuelles des attributs
        foreach($this->dbColumnsConfig as $attributeName => $columnConfig)
            $updateConfig[$attributeName] = $columnConfig->convertFromValue(data: $this->$attributeName);

        $queryBuilder = $this->prepareForNewQuery()->update(updateConfig: $updateConfig);

        // exécution de la requête
        $statement = self::execQuery(
            queryBuilder: self::buildPrimaryKeysCondOn(model: $this,queryBuilder: $queryBuilder)
        );

        if($statement === null)
            return false;

        $this->beforeDelete();

        return true;
    }

    /**
     * @brief Supprime la ligne en base de données, basé sur les clés primaires
     * @return bool
     * @throws ConfigException en cas d'erreur
     * @throws DatabaseActionException en cas d'erreur
     * @throws MysqlException en cas de clé primaire non fourni
     */
    #[Override]
    public function delete(): bool{
        $this->beforeDelete();

        $queryBuilder = $this->prepareForNewQuery()->delete();

        // exécution de la requête
        $statement = self::execQuery(
            queryBuilder: self::buildPrimaryKeysCondOn(model: $this,queryBuilder: $queryBuilder)
        );

        if($statement === null)
            return false;

        $this->afterDelete();

        return true;
    }

    /**
     * @brief Met à jour la valeur d'un attribut
     * @param string $attributeName Nom de l'attribut à mettre à jour
     * @param mixed $value valeur à placer
     * @return $this
     * @throws ConfigException en cas d'attribut non trouvé
     * @throws FormaterException en cas d'erreur de formatage
     * @throws MysqlCondException en cas d'erreur de validation
     */
    public function setAttribute(string $attributeName,mixed $value):MysqlModel{
        $columnConfig = $this->dbColumnsConfig[$attributeName] ?? null;

        if($columnConfig === null)
            throw new ConfigException(message: "Attribut non trouvé");

        // vérification de la validité et formatage de la donnée
        $formatedData = $columnConfig
            ->verifyData(baseModel: $this,attributeName: $attributeName,data: $value)
            ->formatData(baseModel: $this,originalData: $value);

        $this->attributesOriginalValues[$attributeName] = $value;
        $this->$attributeName = $formatedData;

        return $this;
    }

    /**
     * @brief Fourni la valeur de l'attribut
     * @param string $attributeName nom de l'attribut
     * @param bool $reform si true reforme la donnée via les formateurs de reformation
     * @return mixed La donnée
     * @throws ConfigException en cas d'attribut non trouvé
     * @throws FormaterException en cas d'échec de formatage
     */
    public function getAttribute(string $attributeName,bool $reform = true):mixed{
        $columnConfig = $this->dbColumnsConfig[$attributeName] ?? null;

        if($columnConfig === null)
            throw new ConfigException(message: "Attribut non trouvé");

        $data = $this->$attributeName;

        // reformation de la donnée
        if($reform)
            $data = $columnConfig->reformData(baseModel: $this,formatedData: $data);

        return $data;
    }

    /**
     * @brief Fourni la valeur originale non formatée de l'attribut
     * @attention Si la valeur était inséré en base de données l'originale équivaut à la valeur formatée avant insertion
     * @param string $attributeName non de l'attribut
     * @return mixed la valeur ou null
     */
    public function getAttributOriginal(string $attributeName):mixed{
        return $this->attributesOriginalValues[$attributeName] ?? null;
    }

    /**
     * @return TableColumn[]|EnumColumn[] La configuration des colonnes
     */
    public function getColumnsConfig():array{
        return $this->dbColumnsConfig;
    }

    /**
     * @brief Fourni la configuration de colonne d'un attribut en particulier
     * @param string $attributName Nom de l'attribut
     * @return TableColumn|EnumColumn|null la configuration de colonne ou null
     */
    public function getColumnConfig(string $attributName):TableColumn|EnumColumn|null{
        return $this->dbColumnsConfig[$attributName] ?? null;
    }

    /**
     * @return JoinedColumn[] Les configurations des colonnes de jointure
     */
    public function getJoinedColumnsConfig():array{
        return $this->joinedColumnsConfig;
    }

    /**
     * @return TableName Fournisseur du nom de la table
     */
    public function getTableNameManager(): TableName{
        return $this->tableName;
    }

    /**
     * @brief Transforme les données du model en tableau
     * @param bool $addJoinedColumns si true ajoute les colonnes jointes
     * @return array le tableau indicé par les noms d'attributs et comme valeur celles récupérées
     */
    public function getAsArray(bool $addJoinedColumns = true):array{
        $result = [];

        // récupération des attributs
        foreach($this->dbColumnsConfig as $attributeName => $_)
            $result[$attributeName] = $this->getAttribute(attributeName: $attributeName);

        if(!$addJoinedColumns)
            return $result;

        // récupération des colonnes jointes
        foreach($this->joinedColumnsConfig as $attributeName => $_)
            $result[$attributeName] = array_map(
                callback: fn(MysqlModel $joinedModel):array => $joinedModel->getAsArray(),
                array: $this->$attributeName->toArray()
            );

        return $result;
    }

    /**
     * @param array $attributesOriginalValues
     */
    public function setAttributesOriginalValues(array $attributesOriginalValues): void{
        $this->attributesOriginalValues = $attributesOriginalValues;
    }

    #[Override]
    public function afterGeneration(mixed $datas = []): DatabaseModel{
        parent::afterGeneration(datas: $datas);

        // sauvegarde des valeurs par défaut des attributs

        foreach($this->dbColumnsConfig as $attributeName => $_)
            $this->attributesOriginalValues[$attributeName] = $this->$attributeName;

        return $this;
    }

    #[Override]
    protected function beforeCreate(mixed $datas = []): DatabaseModel{
        return parent::beforeCreate(datas: $datas);
    }

    #[Override]
    protected function afterCreate(mixed $datas = []): DatabaseModel{
        return parent::afterCreate(datas: $datas);
    }

    #[Override]
    protected function afterUpdate(mixed $datas = []): DatabaseModel{
        return parent::afterUpdate(datas: $datas);
    }

    #[Override]
    protected function beforeUpdate(mixed $datas = []): DatabaseModel{
        return parent::beforeUpdate(datas: $datas);
    }

    #[Override]
    protected function afterDelete(mixed $datas = []): DatabaseModel{
        return parent::afterDelete(datas: $datas);
    }

    #[Override]
    protected function beforeDelete(mixed $datas = []): DatabaseModel{
        return parent::beforeDelete(datas: $datas);
    }

    /**
     * @brief Action à exécuter avant génération du model
     * @param mixed $datas tableau indicé par les noms d'attributs, et avec comme valeur celle en base de données
     * @return $this
     * @throws DatabaseActionException en cas d'erreur
     */
    #[Override]
    protected function beforeGeneration(mixed $datas = []): MysqlModel{
        return parent::beforeGeneration(datas: $datas);
    }

    /**
     * @brief Charge la configuration du modèle
     * @return void
     * @throws ConfigException en cas de mauvaise configuration
     */
    protected function loadConfiguration():void{
        $reflection = new ReflectionClass(objectOrClass: $this);

        // récupération du nom de la table
        $found = false;

        foreach($reflection->getAttributes() as $attribute){
            if($attribute->getName() === TableName::class){
                $this->tableName = $attribute->newInstance();
                $found = true;
                break;
            }
        }

        if(!$found)
            throw new ConfigException(message: "Model mal configuré");

        // chargement des colonnes lié à la base de donnée
        $this->dbColumnsConfig = [];
        $this->joinedColumnsConfig = [];

        foreach($reflection->getProperties() as $property){
            $propertyName = $property->getName();

            // recherche de l'attribut descriptif
            foreach($property->getAttributes() as $attribute){
                $instance = $attribute->newInstance();

                if($instance instanceof TableColumn){
                    $this->dbColumnsConfig[$propertyName] = $instance;
                    break;
                }

                if($instance instanceOf JoinedColumn){
                    $this->joinedColumnsConfig[$propertyName] = $instance;
                    break;
                }
            }
        }
    }

    /**
     * @brief Prépare le queryBuilder interne pour une nouvelle requête
     * @return MysqlQueryBuilder le queryBuilder prêt pour une nouvelle requête
     */
    protected function prepareForNewQuery():MysqlQueryBuilder{
        return $this->queryBuilder->reset();
    }

    /**
     * @brief Fourni le dernier id inséré
     * @return int|null le dernier id inséré
     * @throws ConfigException en cas d'erreur de configuration
     */
    protected function lastInsertId():int|null{
        $provider = self::getDatabaseConfig()->getConfig(name: DatabaseConfig::PROVIDER->value);

        return $provider->getCon()?->lastInsertId();
    }

    /**
     * @brief Génère un model à partir de la première ligne fetch
     * @param PDOStatement|null $statement statement
     * @param MysqlQueryBuilder $queryBuilder constructeur
     * @return MysqlModel|null le model crée ou null
     * @throws MysqlException en cas d'erreur
     * @throws ConfigException en cas d'erreur
     * @throws DatabaseActionException en cas d'erreur
     */
    public static function createFromDatabaseLine(?PDOStatement $statement,MysqlQueryBuilder $queryBuilder):MysqlModel|null{
        if($statement === null)
            throw new MysqlException(message: "Echec de construction de la requête");

        $lineConfig = $statement->fetch(mode: PDO::FETCH_ASSOC);

        if($lineConfig === null || $lineConfig === false)
            return null;

        return self::createModelFromLine(
            line: $lineConfig,
            modelClass: get_class(object: $queryBuilder->getBaseModel())
        );
    }

    /**
     * @brief Génère un model à partir de la première ligne fetch
     * @param PDOStatement|null $statement statement
     * @param MysqlQueryBuilder $queryBuilder constructeur
     * @return SaboList la liste des models générés
     * @throws MysqlException en cas d'erreur
     * @throws ConfigException en cas d'erreur
     * @throws DatabaseActionException en cas d'erreur
     */
    public static function createFromDatabaseLines(?PDOStatement $statement,MysqlQueryBuilder $queryBuilder):SaboList{
        $models = [];

        while(true){
            $model = self::createFromDatabaseLine(statement: $statement,queryBuilder: $queryBuilder);

            if($model === null)
                break;

            $models[] = $model;
        }

        return new SaboList(datas: $models);
    }

    /**
     * @brief Récupère la première ligne fournie par les résultats de la requête
     * @param MysqlCondition|MysqlCondSeparator ...$findBuilders Configuration de recherche
     * @return MysqlModel|null model trouvé ou null
     * @throws ConfigException en cas d'erreur de configuration
     */
    #[Override]
    public static function findOne(DatabaseCondition|DatabaseCondSeparator ...$findBuilders): MysqlModel|null{
        try{
            $queryBuilder = MysqlQueryBuilder::createFrom(modelClass: get_called_class());

            $queryBuilder->select();

            if(!empty($findBuilders) )
                $queryBuilder->where()->cond(...$findBuilders);

            $queryBuilder->limit(count: 1);

            return self::createFromDatabaseLine(statement: self::execQuery(queryBuilder: $queryBuilder),queryBuilder: $queryBuilder);
        }
        catch(ConfigException $e){
            throw $e;
        }
        catch(Throwable){
            return null;
        }
    }

    /**
     * @brief
     * @param DatabaseCondition|DatabaseCondSeparator ...$findBuilders
     * @return SaboList<MysqlModel>
     * @throws ConfigException en cas d'erreur de configuration
     * @throws MysqlException en cas d'erreur
     * @throws DatabaseActionException en cas d'erreur
     */
    #[Override]
    public static function findAll(DatabaseCondition|DatabaseCondSeparator ...$findBuilders): SaboList{
        $queryBuilder = MysqlQueryBuilder::createFrom(modelClass: get_called_class());

        $queryBuilder->select();

        if(!empty($findBuilders) )
            $queryBuilder->where()->cond(...$findBuilders);

        return self::createFromDatabaseLines(statement: self::execQuery(queryBuilder: $queryBuilder),queryBuilder: $queryBuilder);
    }

    /**
     * @param string $modelClass class du model
     * @return MysqlModel le model créé
     * @throws ConfigException en cas d'erreur
     */
    public static function newInstanceOfModel(string $modelClass):MysqlModel{
        try{
            $reflection = new ReflectionClass(objectOrClass: $modelClass);

            $model = $reflection->newInstance();

            if(!($model instanceof MysqlModel))
                throw new ConfigException(message: "La class fournie doit être une sous class de " . MysqlModel::class);

            return $model;
        }
        catch(ConfigException $e){
            throw $e;
        }
        catch(Throwable){
            throw new ConfigException(message: "Une erreur s'est produite lors de la construction du model");
        }
    }

    /**
     * @brief Exécute la requête et fourni le statement de réponse
     * @param MysqlQueryBuilder $queryBuilder constructeur de requête
     * @param bool $execute si true exécute la requête et fourni le statement sinon fourni le statement
     * @return PDOStatement|null le statement
     * @throws ConfigException en cas d'erreur de configuration
     */
    public static function execQuery(MysqlQueryBuilder $queryBuilder,bool $execute = true):?PDOStatement{
        $provider = self::getDatabaseConfig()->getConfig(name: DatabaseConfig::PROVIDER->value);

        $statement = $queryBuilder->prepareRequest(pdo: $provider->getCon());

        if($statement === null || ($execute && !$statement->execute() ) )
            return null;

        return $statement;
    }

    /**
     * @brief Charge les données de la colonne jointe fournie
     * @param MysqlModel $model model de base dans laquelle charger les données
     * @param JoinedColumn $joinedColumn Configuration de jointure
     * @return SaboList<MysqlModel> Résultats de la récupération
     * @throws MysqlException en cas d'erreur
     */
    public static function loadJoinedColumns(MysqlModel $model,JoinedColumn $joinedColumn):SaboList{
        $joinConfig = $joinedColumn->getJoinConfig();

        // construction des conditions de match
        $conditions = [MysqlCondSeparator::GROUP_START()];

        foreach($joinConfig as $baseModelAttributeName => $joinModelAttributeName){
            $conditions[] = new MysqlCondition(
                condGetter: $joinModelAttributeName,
                comparator: MysqlComparator::EQUAL(),
                conditionValue: $model->$baseModelAttributeName
            );

            $conditions[] = MysqlCondSeparator::AND();
        }

        $size = count(value: $conditions);

        if($size === 1)
            throw new MysqlException(message: "Aucune condition de match sur le liste jointe",isDisplayable: false);

        // remplacement du dernier and par la fermeture de groupe
        $conditions[$size - 1] = MysqlCondSeparator::GROUP_END();

        return @call_user_func_array([$joinedColumn->getClassModel(),"findAll"],$conditions);
    }

    /**
     * @brief Crée un model à partir de la configuration
     * @param array $line contenu de la ligne de la base de données
     * @param string $modelClass class du model
     * @return MysqlModel model crée
     * @throws ConfigException en cas d'erreur
     * @throws MysqlException en cas d'erreur
     * @throws DatabaseActionException en cas d'erreur
     */
    public static function createModelFromLine(array $line,string $modelClass):MysqlModel{
        $model = self::newInstanceOfModel(modelClass: $modelClass);

        $columnsConfig = $model->getColumnsConfig();

        // tableau indicé par les noms d'attributs et les valeurs de la ligne
        $linkedValues = [];

        // construction du tableau inversé des configurations
        foreach($line as $columnRealName => $dbValue){
            foreach($columnsConfig as $attributeName => $columnConfig){
                if($columnConfig->getColumnName() === $columnRealName){
                    $linkedValues[$attributeName] = $dbValue;

                    break;
                }
            }
        }

        // exécution des actions pré génération
        $model->beforeGeneration(datas: $linkedValues);

        // affectation des attributs
        foreach($linkedValues as $attributeName => $dbValue)
            $model->$attributeName = $columnsConfig[$attributeName]->convertToValue(data: $dbValue);

        // chargement des colonnes jointes
        foreach($model->joinedColumnsConfig as $attributeName => $config){
            $list = new JoinedList(descriptor: $config,linkedModel: $model);
            $model->$attributeName = $list;

            if(!$config->getLoadOnGeneration())
                continue;

            $list->loadContent();
        }


        // exécution des actions post générations
        $model->afterGeneration();

        return $model;
    }

    /**
     * @brief Ajoute au queryBuilder les conditions de vérification de clé primaire du model
     * @param MysqlModel $model le model
     * @param MysqlQueryBuilder $queryBuilder constructeur
     * @param bool $addWhere si true ajoute ->where() suivi des conditions si false ajoute une condition AND avant d'ajouter le groupe de vérification des clés primaires
     * @return MysqlQueryBuilder le constructeur changé
     * @throws MysqlException en cas de clés primaires non présente
     */
    public static function buildPrimaryKeysCondOn(MysqlModel $model,MysqlQueryBuilder $queryBuilder,bool $addWhere = true):MysqlQueryBuilder{
        if($addWhere)
            $queryBuilder->where();
        else
            $queryBuilder->cond(MysqlCondSeparator::AND());

        // ajout du groupe de conditions
        $columnsConfig = $model->getColumnsConfig();
        $primaryKeysCond = [];

        foreach($columnsConfig as $attributeName => $columnConfig){
            if($columnConfig->isPrimaryKey()){
                $primaryKeysCond[] = new MysqlCondition(
                    condGetter: $attributeName,
                    comparator: MysqlComparator::EQUAL(),
                    conditionValue: $columnConfig->convertFromValue(data: $model->$attributeName)
                );
                $primaryKeysCond[] = MysqlCondSeparator::AND();
            }
        }

        if(empty($primaryKeysCond) )
            throw new MysqlException(message: "Aucune clé primaire trouvée",isDisplayable: false);

        // remplacement de la dernière condition AND
        $primaryKeysCond[count(value: $primaryKeysCond) - 1] = MysqlCondSeparator::GROUP_END();

        return $queryBuilder->cond(
            MysqlCondSeparator::GROUP_START(),
            ...$primaryKeysCond
        );
    }

    /**
     * @return Config la configuration de la base de donnée de l'application
     * @throws ConfigException en cas d'erreur de configuration
     */
    protected static function getDatabaseConfig():Config{
        return Application::getEnvConfig()->getConfig(name: EnvConfig::DATABASE_CONFIG->value);
    }
}
