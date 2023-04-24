<?php

namespace Sabo\Model\System\Mysql;

use PDO;
use Exception;
use PDOException;
use Sabo\Config\EnvConfig;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Model\Model\SaboModel;
use Sabo\Model\System\Interface\System;
use Sabo\Model\System\QueryBuilder\QueryBuilder;
use Sabo\Model\System\QueryBuilder\SqlComparator;
use Sabo\Model\System\QueryBuilder\SqlSeparator;

/**
 * représente une base de donnée MYSQL
 */
abstract class SaboMysql implements System{
    /**
     * identifiants de connexion à la base de données
     */
    protected static array $databaseConfig;

    /**
     * connexion partagé entre les modèles
     */
    protected static ?PDO $sharedCon = null;

    /**
     * connexion du modèle
     */
    protected ?PDO $myCon = null;

    /**
     * constructeur de requête lié au modèle
     */
    protected ?QueryBuilder $queryBuilder = null;

    /**
     * remplace le constructeur de la class
     */
    abstract protected function pseudoConstruct():void;

    /**
     * @param createNewCon défini si une nouvelle connexion doit être crée (si faux alors connexion partagé utilisé)
     */
    public function __construct(bool $createNewCon = false){            
        $this->pseudoConstruct();
        
        $this->myCon = $createNewCon ? self::getNewCon() : self::$sharedCon;
    }

    /**
     * insère le model dans la base de données
     * @return bool si la requête a réussi
     */
    public function insert():bool{
        $this->initQueryBuilder();

        $linkedModel = $this->queryBuilder->getLinkedModel();

        $toInsert = [];

        // création du tableau de valeur à insérer
        foreach($linkedModel->getColumnsConfiguration() as $attributeName => $configuration){
            if(!empty($configuration["configClass"]) && !$configuration["configClass"]->getIsAutoIncremented() ) $toInsert[$attributeName] = $linkedModel->getAttribute($attributeName);
        }

        $this->queryBuilder->insert($toInsert);

        return self::execQuery($this->queryBuilder);
    }

    /**
     * supprime le model de la base de donnée
     * @return bool si la requête a réussi 
     */
    public function delete():bool{
        $this->initQueryBuilder();

        $this->queryBuilder
            ->delete()
            ->addPrimaryKeysWhereCond();

        return self::execQuery($this->queryBuilder);
    }

    /**
     * supprime le model de la base de donnée
     * @return bool si la requête a réussi 
     */
    public function update():bool{
        $this->initQueryBuilder();

        $linkedModel = $this->queryBuilder->getLinkedModel();
        $columnsConfiguration = $linkedModel->getColumnsConfiguration();

        $toUpdate = [];

        // création du tableau de valeur à update
        foreach($columnsConfiguration as $attributeName => $configuration){
            if(!empty($configuration["configClass"]) ) $toUpdate[$attributeName] = $toUpdate[$attributeName] = $linkedModel->getAttribute($attributeName); 
        }

        $this->queryBuilder
            ->update($toUpdate)
            ->addPrimaryKeysWhereCond();

        return self::execQuery($this->queryBuilder);
    }

    /**
     * démarre une transaction sur la connexion du model
     * @throws Exception (en mode debug) si la connexion est null
     */
    public function beginTransaction():bool{
        return self::beginTransactionOn($this->myCon);
    }

    /**
     * commit une transaction sur la connexion du model
     * @throws Exception (en mode debug) si la connexion est null
     */
    public function commitTransaction():bool{
        return self::commitTransactionOn($this->myCon);
    }

    /**
     * rollback une transaction sur la connexion du model
     * @throws Exception (en mode debug) si la connexion est null
     */
    public function rollbackTransaction():bool{
        return self::rollbackTransactionOn($this->myCon);
    }

    /**
     * @return PDO|null la connexion
     */
    public function getMyCon():?PDO{
        return $this->myCon;
    }

    /**
     * @param resetBefore défini si le querybuilder doit être reset avant d'être renvoyé
     * @return QueryBuilder le query builder interne au model (non reset)
     */
    public function getQueryBuilder(bool $resetBefore = true):QueryBuilder{
        if($this->queryBuilder == null) 
            $this->initQueryBuilder();
        else if($resetBefore)
            $this->queryBuilder->reset();

        return $this->queryBuilder;
    }

    /**
     * initialise le créateur de requête interne si non défini
     */
    private function initQueryBuilder():SaboMysql{
        if($this->queryBuilder == null) $this->queryBuilder = new QueryBuilder($this);

        $this->queryBuilder->reset();

        return $this;
    }

    /**
     * cherche des résultats en base de données à partir de conditions
     * @param conds conditions à vérifier, format [attribute_name => value] ou [attribute_name => [value,SqlComparator,(non obligatoire and par défaut)] SqlSeparator and ou or]
     * @param toSelect le nom des attributs liés aux colonnes à récupérer
     * @param getBaseResult défini si les résultats doivent être retournés telles qu'elles (pdostatement) ou sous forme d'objets
     * @return mixed un tableau contenant les objets si résultats multiples ou un objet model si un seul résultat ou pdostatement de la requête si getBaseResult à true ou null si aucun résultat
     * @throws Exception (en mode debug) si données mal formulés 
     */
    public static function find(array $conds = [],array $toSelect = [],bool $getBaseResult = false):mixed{
        $queryBuilder = QueryBuilder::createFrom(get_called_class() );

        $queryBuilder->select(...$toSelect);

        // vérification des conditions
        if(!empty($conds) ){
            $whereConds = [];

            // création des conditions where
            foreach($conds as $attributeName => $condData){
                if(gettype($condData) == "array"){
                    $data = [
                        $attributeName,
                        ...$condData
                    ];

                    if(empty($condData[2]) ) array_push($data,SqlSeparator::AND);

                    array_push($whereConds,$data);
                }
                else array_push($whereConds,[$attributeName,$condData,SqlComparator::EQUAL,SqlSeparator::AND]);
            }

            // suppression du dernier separateur and ou or
            unset($whereConds[count($whereConds) - 1][3]);

            // ajout de la clause where
            $queryBuilder   
                ->where()
                ->whereGroup(...$whereConds);
        }

        return self::execQuery($queryBuilder,$getBaseResult ? MysqlReturn::DEFAULT : MysqlReturn::OBJECTS);
    }

    /**
     * initialise les modèles
     * @return bool état de succès
     */
    public static function initModel():bool{
        $configEnv = EnvConfig::getConfigEnv();

        // récupération des identifiants de base de données
        if(EnvConfig::getIsJsonEnv() ){
            if(!isset($configEnv["database"]["host"],$configEnv["database"]["user"],$configEnv["database"]["password"],$configEnv["database"]["name"]) ) throw new Exception("Missed a database key in configuration");

            self::$databaseConfig = [
                "host" => $configEnv["database"]["host"],
                "user" => $configEnv["database"]["user"],
                "password" => $configEnv["database"]["password"],
                "name" => $configEnv["database"]["name"]
            ];

            if(isset($configEnv["database"]["port"]) ) self::$databaseConfig["port"] = $configEnv["database"]["port"];
        }
        else{
            if(!isset($configEnv["DB_HOST"],$configEnv["DB_USER"],$configEnv["DB_PASSWORD"],$configEnv["DB_NAME"]) ) throw new Exception("Missed a database key in configuration");

            self::$databaseConfig = [
                "host" => $configEnv["DB_HOST"],
                "user" => $configEnv["DB_USER"],
                "password" => $configEnv["DB_PASSWORD"],
                "name" => $configEnv["DB_NAME"]
            ];

            if(isset($configEnv["DB_PORT"]) ) self::$databaseConfig["port"] = $configEnv["DB_PORT"];
        }   

        self::$sharedCon = static::getNewCon();

        return self::$sharedCon != null;
    }

    /**
     * démarre une transaction sur la connexion partagé
     * @throws Exception (en mode debug) si la connexion est null
     */
    public static function beginTransactionOnShared():bool{
        return self::beginTransactionOn(self::$sharedCon);
    }

    /**
     * commit une transaction sur la connexion partagé
     * @throws Exception (en mode debug) si la connexion est null
     */
    public static function commitTransactionOnShared():bool{
        return self::commitTransactionOn(self::$sharedCon);
    }

    /**
     * rollback une transaction sur la connexion partagé
     * @throws Exception (en mode debug) si la connexion est null
     */
    public static function rollbackTransactionOnShared():bool{
        return self::rollbackTransactionOn(self::$sharedCon);
    }

    /**
     * execute la requête contenue dans un le queryBuilder
     * @param queryBuilder le queryBuilder à exécuter
     * @param toReturn le type de donnée à retourner, par défaut success_state 
     * @return mixed
     */
    public static function execQuery(QueryBuilder $queryBuilder,MysqlReturn $toReturn = MysqlReturn::SUCCESS_STATE):mixed{
        $linkedModel = $queryBuilder->getLinkedModel();
        
        $pdo = $linkedModel->getMyCon();

        $query = $pdo->prepare($queryBuilder->getSqlString() );

        if($query != false)
		{
            // ajout des valeurs à bind
            $toBind = $queryBuilder->getToBind();

			foreach($toBind as $key => $bindData)
			{
				if(gettype($bindData) == "array")
					$query->bindValue($key + 1,...$bindData);
				else
					$query->bindValue($key + 1,gettype($bindData) == "boolean" ? ($bindData === false ? 0 : 1) : $bindData);
			}

			if($query->execute() ){
                switch($toReturn){
                    case MysqlReturn::DEFAULT: return $query;
                    case MysqlReturn::SUCCESS_STATE : return true;
                    case MysqlReturn::OBJECTS:
                        // création des objets model à retourner
                        $objects = [];   

                        foreach($query->fetchAll() as $rowData) array_push($objects,static::createObjectFrom($linkedModel,$rowData) );

                        return $objects;
                    ;
                }
            }
		}

        switch($toReturn){
            case MysqlReturn::DEFAULT: return null;
            case MysqlReturn::SUCCESS_STATE : return false;
            case MysqlReturn::OBJECTS: return [];
        }
    }

    /**
     * @return PDO|null la connexion partagé entre les modèles
     */
    public static function getSharedCon():?PDO{
        return self::$sharedCon;
    }

    /**
     * @return PDO|null une nouvelle connexion à la base de données
     */
    protected static function getNewCon():?PDO{
        try{
            list("host" => $host,"user" => $user,"name" => $name,"password" => $password) = self::$databaseConfig;

            $port = !empty(self::$databaseConfig["port"]) ? "port=" . self::$databaseConfig["port"] . ";" : "";

            $pdo = new PDO("mysql:host={$host};{$port}dbname={$name};charset=UTF8",$user,$password,[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            $pdo->exec("set names utf8");

            return $pdo;
        }
        catch(Exception $e){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) ) throw $e;

            return null;
        }

    }

    /**
     * lance une transaction avec la connexion
     * @param con la connexion sur laquelle démarrer la connexion
     * @return bool si réussi
     * @throws Exception (en mode debug) si la connexion est null
     */
    protected static function beginTransactionOn(?PDO $con):bool{
        if($con == null){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw new Exception("La connexion est null, démarrage de transaction échoué");
            else
                return false;
        }   

        try{
            return $con->beginTransaction();
        }
        catch(PDOException){
            return false;
        }
    }

    /**
     * commit une transaction avec la connexion
     * @param con la connexion sur laquelle commit la connexion
     * @return bool si réussi
     * @throws Exception (en mode debug) si la connexion est null
     */
    protected static function commitTransactionOn(?PDO $con):bool{
        if($con == null){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw new Exception("La connexion est null, commit de transaction échoué");
            else
                return false;
        }   

        try{
            return $con->commit();
        }
        catch(PDOException){
            return false;
        }
    }

    /**
     * rollback une transaction avec la connexion
     * @param con la connexion sur laquelle rollback la connexion
     * @return bool si réussi
     * @throws Exception (en mode debug) si la connexion est null
     */
    protected static function rollbackTransactionOn(?PDO $con):bool{
        if($con == null){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw new Exception("La connexion est null, commit de transaction échoué");
            else
                return false;
        }   

        try{
            return $con->rollBack();
        }
        catch(PDOException){
            return false;
        }
    }

    /**
     * crée un objet sabomodel à partir de la ligne passé en base de donnée
     * @param linkedModel une instance du model à crée
     * @param rowData la ligne de la base de données [format fetchAssoc]
     * @return SaboModel|null le model ou null
     * @throws Exception (en mode debug) si rowData est mal formé
     */
    protected static function createObjectFrom(SaboModel $linkedModel,array $rowData):?SaboModel{
        if(!empty($rowData) ){
            $columnsConfiguration = $linkedModel->getColumnsConfiguration();

            // création d'une nouvelle instance du model
            $model = $linkedModel->getReflection()->newInstance();

            $foundName = false;

            foreach($rowData as $attributeCol => $value){
                // recherche du nom de l'attribut lié à cette colonne
                foreach($columnsConfiguration as $attributeName => $configuration){
                    if(empty($configuration["configClass"]) ) continue;

                    if($configuration["configClass"]->getLinkedColName() == $attributeCol){
                        $foundName = true;

                        $model->{$attributeName} = $value;

                        break;
                    }
                }
            }   

            if(!$foundName){
                if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                    throw new Exception("Aucun attribut trouvé pour la colonne {$attributeCol}");
                else
                    return null;
            }

            return $model;
        }
        else if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) ) throw new Exception("Le row data est mal formé");

        return null;
    }
}