<?php

namespace Sabo\Model\System\Mysql;

use PDO;
use Exception;
use PDOException;
use Sabo\Config\EnvConfig;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Model\System\Interface\System;
use Sabo\Model\System\QueryBuilder\QueryBuilder;

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
     * insère le model dans la base de données
     * @return bool si la requête a réussi
     */
    public function insert():bool{


        return false;
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

        die($this->queryBuilder->getSqlString() );

        return false;
    }

    /**
     * supprime le model de la base de donnée
     * @return bool si la requête a réussi 
     */
    public function update():bool{
        return false;
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
     * initialise le créateur de requête interne si non défini
     */
    private function initQueryBuilder():SaboMysql{
        if($this->queryBuilder == null) $this->queryBuilder = new QueryBuilder($this);

        $this->queryBuilder->reset();

        return $this;
    }

    /**
     * cherche des résultats en base de données à partir de conditions
     * @param conds conditions à vérifier
     * @param getBaseResult défini si les résultats doivent être retournés telles qu'elles ou sous forme d'objets
     * @return bool si la requête a réussi 
     */
    public static function find(array $conds,bool $getBaseResult = false):mixed{

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
}