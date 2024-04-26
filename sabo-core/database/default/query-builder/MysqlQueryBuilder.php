<?php

namespace SaboCore\Database\Default\QueryBuilder;

use PDO;
use PDOStatement;
use SaboCore\Config\ConfigException;
use SaboCore\Database\Default\Attributes\TableColumn;
use SaboCore\Database\Default\System\MysqlCondition;
use SaboCore\Database\Default\System\MysqlCondSeparator;
use SaboCore\Database\Default\System\MysqlException;
use SaboCore\Database\Default\System\MysqlFunction;
use SaboCore\Database\Default\System\MysqlModel;
use SaboCore\Database\Default\System\MysqlBindDatas;
use Throwable;

/**
 * @brief Constructeur de requête
 * @author yahaya bathily https://github.com/yahvya
 */
class MysqlQueryBuilder{
    /**
     * @var string Chaine sql de la requête
     */
    protected string $sqlString;

    /**
     * @var MysqlBindDatas[] Valeur à bind
     */
    protected array $toBind;

    /**
     * @var MysqlModel Model de base
     */
    protected MysqlModel $baseModel;

    /**
     * @var string Alias de la table
     */
    protected string $tableAlias;

    /**
     * @param MysqlModel $model instance du model
     */
    public function __construct(MysqlModel $model){
        $this->baseModel = $model;
        $this->reset();
    }

    /**
     * @brief Crée un query builder à partir du model fourni
     * @param string $modelClass model de la class
     * @return MysqlQueryBuilder l'instance crée
     * @throws ConfigException en cas d'erreur de configuration
     */
    public static function createFrom(string $modelClass):MysqlQueryBuilder{
        return new MysqlQueryBuilder(model: MysqlModel::newInstanceOfModel(modelClass: $modelClass) );
    }

    /**
     * @brief Remet à 0 le contenu du QueryBuilder
     * @return $this
     */
    public function reset():MysqlQueryBuilder{
        $this->sqlString = "";
        $this->toBind = [];
        $this->tableAlias = $this->baseModel->getTableNameManager()->getTableName() . time();

        return $this;
    }

    /**
     * @brief Prépare la requête
     * @param PDO $pdo instance pdo
     * @return PDOStatement|null Résultat de la préparation
     */
    public function prepareRequest(PDO $pdo):?PDOStatement{
        try{
            $statement = $pdo->prepare(query: $this->getSql());

            if($statement === false)
                return null;

            // ajout des valeurs à bind
            $toBind = $this->getBindValues();
            $bindCounter = 0;

            foreach($toBind as $bindManager){
                foreach($bindManager->getToBindDatas() as $bindConfig){
                    $bindCounter++;
                    $statement->bindValue($bindCounter,...$bindConfig);
                }
            }

            return $statement;
        }
        catch(Throwable){
            return null;
        }
    }

    /**
     * @brief Met à jour l'alias de la table
     * @param string $alias nouvel alias
     * @return $this
     */
    public function as(string $alias):MysqlQueryBuilder{
        $this->tableAlias = $alias;

        return $this;
    }

    /**
     * @return string La chaine sql sans modification
     * @attention La chaine fournie peut ne pas être utilisable
     */
    public function getRealSql():string{
        return $this->sqlString;
    }

    /**
     * @return string La chaine sql formaté pour une requête
     */
    public function getSql():string{
        return str_replace(
            search: ["{aliasTable}"],
            replace: [$this->tableAlias],
            subject: $this->sqlString
        );
    }

    /**
     * @return MysqlModel instance du model de base
     */
    public function getBaseModel():MysqlModel{
        return $this->baseModel;
    }

    /**
     * @return MysqlBindDatas[] les valeurs à bind
     */
    public function getBindValues():array{
        return $this->toBind;
    }

    /**
     * @brief Join la requête fournie dans la requête actuelle
     * @param MysqlQueryBuilder $toJoin Builder à joindre
     * @param string|null $sqlBefore Chaine sql à placer à avant ou null
     * @param string|null $sqlAfter Chaine sql à placer après ou null
     * @return $this
     */
    public function joinBuilder(MysqlQueryBuilder $toJoin,?string $sqlBefore = null,?string $sqlAfter = null):MysqlQueryBuilder{
        $this->sqlString .= ($sqlBefore ?? "") . $toJoin->getSql() . ($sqlAfter ?? "");
        $this->toBind = array_merge($this->toBind,$toJoin->getBindValues());

        return $this;
    }

    /**
     * @brief Récupère les valeurs à bind en fonction de la valeur et fourni la chaine sql résultante
     * @attention Ne modifie pas la chaine sql
     * @param TableColumn $columnConfig Configuration de la colonne traitée
     * @param mixed|MysqlFunction|MysqlQueryBuilder $data le type de donnée à traiter
     * @param string|null $sqlBefore Chaine sql à placer avant en cas de Builder
     * @param string|null $sqlAfter Chaine sql à placer après en cas de Builder
     * @return array les données au format ["sql" => ...,"toBind" => [MysqlBindDatas, ...]
     */
    protected function manageValueDatas(TableColumn $columnConfig,mixed $data,?string $sqlBefore = null,?string $sqlAfter = null):array{
        if($data instanceof MysqlQueryBuilder){
            return [
                "sql" => ($sqlBefore ?? "") . $data->getSql() . ($sqlAfter ?? ""),
                "toBind" => $data->getBindValues()
             ];
        }
        else if($data instanceof MysqlFunction){
            // traitement de la "fonction"
            $alias = $data->getAlias();
            $function = $data->getFunction();

            if($data->haveToReplaceAttributesName())
                $function = $this->replaceAttributesNameIn(string: $function);

            return [
                // traitement de l'alias
                "sql" => $function . ($alias ? " AS $alias" : ""),
                "toBind" => []
            ];
        }
        else{
            $toBind = new MysqlBindDatas(
                countOfMarkers: 1,
                toBindDatas: [ [$data,$data === null ? PDO::PARAM_NULL : $columnConfig->getColumnType()] ]
            );

            return [
                "sql" => $toBind->getMarkersStr(),
                "toBind" => [$toBind]
            ];
        }
    }

    /**
     * @brief Remplace le nom des attributs par le nom de colonne associé
     * @param string $string chaine à traiter
     * @return string résultat
     * @attention Le nom d'un attribut doit être placé entre {}
     */
    protected function replaceAttributesNameIn(string $string):string{
        $tableColumnsConfig = $this->baseModel->getColumnsConfig();

        // remplacement des valeurs
        foreach($tableColumnsConfig as $attributeNameToReplace => $attributeConfig)
            $string = @str_replace(search: "{{$attributeNameToReplace}}",replace: $attributeConfig->getColumnName(),subject: $string);

        return $string;
    }

    /**
     * @brief Parse les données de la condition
     * @param MysqlCondition $condition condition
     * @return array les données de la condition ["sql" => ...,"toBind" => MysqlBindDatas]
     */
    protected function parseCondition(MysqlCondition $condition):array{
        $comparator = $condition->getComparator();
        $condGetter = $condition->getCondGetter();

        if($condGetter instanceof MysqlFunction){
            // traitement de la fonction
            $function = $condGetter->getFunction();

            if($condGetter->haveToReplaceAttributesName())
                $function = $this->replaceAttributesNameIn(string: $function);

            $sql = "$function ";
        }
        else{
            // récupération du nom de l'attribut
            $sql = "{$this->baseModel->getColumnConfig(attributName: $condGetter)->getColumnName()} ";
        }

        // traitement de replacement des marqueurs de comparaison
        $toBind = $comparator->getBindDatas(value: $condition->getConditionValue());

        $comparatorStr = str_replace(
            search: ["{singleMarker}","{bindMarkers}"],
            replace: ["?",$toBind->getMarkersStr()],
            subject: $comparator->getComparator()
        );

        return [
            "sql" => $sql . $comparatorStr,
            "toBind" => $toBind
        ];
    }

    /**
     * @brief Parse une séquence de conditions et de séparateurs
     * @param (MysqlCondition|MysqlCondSeparator)[] $sequence séquence
     * @return array les données de la séquence ["sql" => ...,"toBind" => [MysqlBindDatas, ...]]
     */
    protected function parseConditionSequence(array $sequence):array{
        $sql = "";
        $toBindList = [];

        foreach($sequence as $conditionConfig){
            if($conditionConfig instanceof MysqlCondSeparator){
                // traitement du séparateur
                $sql .= "{$conditionConfig->getSeparator()} ";
                continue;
            }

            // traitement de la condition
            ["sql" => $parsedSql,"toBind" => $toBind] = $this->parseCondition($conditionConfig);

            $sql .= "$parsedSql ";
            $toBindList[] = $toBind;
        }

        return [
            "sql" => $sql,
            "toBind" => $toBindList
        ];
    }

    /**
     * @brief Fonctions de requêtes
     */

    /**
     * @brief Démarre une requête statique
     * @param string $sqlString requête sql
     * @param MysqlBindDatas[] $toBind Valeur à bind
     * @param bool $justConcat Si true concatène sinon remplace
     * @return $this
     */
    public function staticRequest(string $sqlString,array $toBind = [],bool $justConcat = false):MysqlQueryBuilder{
        if($justConcat){
            $this->sqlString .= $sqlString;
            $this->toBind = array_merge($this->toBind,$toBind);
        }
        else{
            $this->sqlString = $sqlString;
            $this->toBind = $toBind;
        }

        return $this;
    }

    /**
     * @brief Ajoute la chaine SELECT [] FROM table
     * @param string|MysqlFunction ...$toSelect
     * @return $this
     * @attention en fonction des champs sélectionnés le / les models générés seront partiellement construit s'il manque des champs.
     */
    public function select(string|MysqlFunction ...$toSelect):MysqlQueryBuilder{
        $this->sqlString .= "SELECT ";

        $tableColumnsConfig = $this->baseModel->getColumnsConfig();

        $columnsToSelect = [];

        // remplacement des données à sélectionner
        foreach($toSelect as $value){
            if(gettype($value) === "string"){
                $columnsToSelect[] = $tableColumnsConfig[$value]->getColumnName();
                continue;
            }

            // traitement de la "fonction"
            $alias = $value->getAlias();
            $function = $value->getFunction();

            if($value->haveToReplaceAttributesName())
                $function = $this->replaceAttributesNameIn(string: $function);

            // traitement de l'alias et ajout dans la liste des colonnes sélectionnées
            $columnsToSelect[] = $function . ($alias ? " AS $alias" : "");
        }

        $this->sqlString .= (empty($columnsToSelect) ? "*" : implode(separator: ",",array: $columnsToSelect)) . " FROM {$this->baseModel->getTableNameManager()->getTableName()} AS {aliasTable} ";

        return $this;
    }

    /**
     * @brief Ajoute la chaine INSERT INTO
     * @param MysqlFunction[]|MysqlQueryBuilder|mixed $insertConfig Tableau indicé par le nom des attributs à changer et avec valeur associé valeur|MysqlFunction|MysqlQueryBuilder
     * @return $this
     * @attention en cas de fonction ne pas y placer d'alias
     */
    public function insert(array $insertConfig):MysqlQueryBuilder{
        $this->sqlString .= "INSERT INTO {$this->baseModel->getTableNameManager()->getTableName()} ";

        $columnsToInsert = [];
        $sql = [];
        $columnsConfig = $this->baseModel->getColumnsConfig();

        // ajout des attributs à insérer
        foreach($insertConfig as $attributeName => $value){
            ["sql" => $setSql,"toBind" => $valuesToBind] =  $this->manageValueDatas(
                columnConfig: $columnsConfig[$attributeName],
                data: $value,
                sqlBefore: "(",
                sqlAfter: ")"
            );

            $columnsToInsert[] = $columnsConfig[$attributeName]->getColumnName();
            $sql[] = "$setSql";

            $this->toBind = array_merge($this->toBind,$valuesToBind);
        }

        $this->sqlString .= "(" . implode(separator: ",",array: $columnsToInsert) .") VALUES(" . implode(separator: ",",array: $sql) . ")";

        return $this;
    }

    /**
     * @brief Ajoute la chaine UPDATE table SET []
     * @param MysqlFunction[]|MysqlQueryBuilder|mixed $updateConfig Tableau indicé par le nom des attributs à changer et avec valeur associé valeur|MysqlFunction|MysqlQueryBuilder
     * @return $this
     * @attention en cas de fonction ne pas y placer d'alias
     */
    public function update(array $updateConfig):MysqlQueryBuilder{
        $this->sqlString .= "UPDATE {$this->baseModel->getTableNameManager()->getTableName()} AS {aliasTable} SET ";

        $columnsConfig = $this->baseModel->getColumnsConfig();

        // construction du sql set
        $sql = [];

        // ajout des attributs à modifier
        foreach($updateConfig as $attributeName => $newValue){
            ["sql" => $setSql,"toBind" => $valuesToBind] =  $this->manageValueDatas(
                columnConfig: $columnsConfig[$attributeName],
                data: $newValue,
                sqlBefore: "(",
                sqlAfter: ")"
            );

            $sql[] = "{$columnsConfig[$attributeName]->getColumnName()} = $setSql";

            $this->toBind = array_merge($this->toBind,$valuesToBind);
        }

        $this->sqlString .= implode(separator: ", ",array: $sql) . " ";

        return $this;
    }

    /**
     * @brief Ajoute la chaine DELETE FROM table
     * @return $this
     */
    public function delete():MysqlQueryBuilder{
        $this->sqlString .= "DELETE FROM {$this->baseModel->getTableNameManager()->getTableName()} AS {aliasTable} ";

        return $this;        
    }

    /**
     * @brief Ajoute la chaine WHERE
     * @return $this
     */
    public function where():MysqlQueryBuilder{
        $this->sqlString .= "WHERE ";

        return $this;
    }

    /**
     * @brief Ajoute les conditions where
     * @param MysqlCondition|MysqlCondSeparator ...$conditions conditions de vérification
     * @return $this
     */
    public function cond(MysqlCondition|MysqlCondSeparator ...$conditions):MysqlQueryBuilder{
        ["sql" => $sql,"toBind" => $toBind] =  $this->parseConditionSequence(sequence: $conditions);

        $this->sqlString .= "$sql ";
        $this->toBind = array_merge($this->toBind,$toBind);

        return $this;
    }

    /**
     * @brief Ajoute la chaine HAVING ...
     * @param MysqlCondition|MysqlCondSeparator ...$conditions conditions de vérification
     * @return $this
     */
    public function having(MysqlCondition|MysqlCondSeparator ...$conditions):MysqlQueryBuilder{
        ["sql" => $sql,"toBind" => $toBind] =  $this->parseConditionSequence(sequence: $conditions);

        $this->sqlString .= "HAVING $sql ";
        $this->toBind = array_merge($this->toBind,$toBind);

        return $this;
    }

    /**
     * @brief Ajoute la chaine ORDER BY ... ($builder->orderBy(["price","ASC"],["id","DESC"] ) )
     * @param array ...$configs Tableaux de deux éléments contenant en premier le nom de l'attribut suivi de "ASC" ou "DESC"
     * @return $this
     */
    public function orderBy(array ...$configs):MysqlQueryBuilder{
        $this->sqlString .= "ORDER BY ";
        $sql = [];

        foreach($configs as $orderConfig){
            [$attributeName,$sortOrder] = $orderConfig;

            $sql[] = "{$this->baseModel->getColumnConfig(attributName: $attributeName)->getColumnName()} $sortOrder";
        }

        $this->sqlString .= implode(separator: ",",array: $sql) . " ";

        return $this;
    }

    /**
     * @brief Ajoute la chaine GROUP BY
     * @param string ...$attributesNames nom des attributs
     * @return $this
     */
    public function groupBy(string ...$attributesNames):MysqlQueryBuilder{
        $this->sqlString .= "GROUP BY " . implode(
            separator: ",",
            array: array_map(
                callback: fn(string $attributeName):string => $this->baseModel->getColumnConfig(attributName: $attributeName)->getColumnName(),
                array: $attributesNames
            )
        ) . " ";

        return $this;
    }

    /**
     * @brief Ajoute la clause limit
     * @param int $count Nombre d'éléments
     * @param int|null $offset Offset
     * @return $this
     */
    public function limit(int $count,?int $offset = null):MysqlQueryBuilder{
        if($offset == null){
            $this->sqlString .= "LIMIT ? ";
            $this->toBind[] = new MysqlBindDatas(
                countOfMarkers: 1,
                toBindDatas: [ [$count,PDO::PARAM_INT] ]
            );
        }
        else{
            $this->sqlString .= "LIMIT ? OFFSET ? ";
            $this->toBind[] = new MysqlBindDatas(
                countOfMarkers: 2,
                toBindDatas: [ [$count,PDO::PARAM_INT],[$offset,PDO::PARAM_INT] ]
            );
        }

        return $this;
    }
}