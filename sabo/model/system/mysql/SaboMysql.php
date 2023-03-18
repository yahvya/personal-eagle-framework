<?php

namespace Sabo\Model\System\Mysql;

use PDO;
use Exception;
use Sabo\Config\EnvConfig;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Model\System\Interface\System;

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
     * @return PDO|null la connexion
     */
    public function getMyCon():?PDO{
        return $this->myCon;
    }

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
     * @return PDO|null la connexion partagé entre les modèles
     */
    public static function getSharedCon():?PDO{
        return self::$sharedCon;
    }
}