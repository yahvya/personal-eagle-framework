<?php

namespace Sabo\Model\System\Postgre;

use Exception;
use PDO;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Model\System\Mysql\SaboMysql;

abstract class SaboPostgre extends SaboMysql{
    /**
     * @return PDO|null une nouvelle connexion à la base de données
     */
    protected static function getNewCon():?PDO{
        try{
            list("host" => $host,"user" => $user,"name" => $name,"password" => $password) = self::$databaseConfig;

            $port = !empty(self::$databaseConfig["port"]) ? "port=" . self::$databaseConfig["port"] . ";" : "";

            $pdo = new PDO("pgsql:host={$host};{$port}dbname={$name};charset=UTF8",$user,$password,[
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
}