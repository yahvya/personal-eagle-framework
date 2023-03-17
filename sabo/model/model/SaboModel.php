<?php

namespace Sabo\Model\Model;

use PDO;
use Exception;
use Sabo\Config\EnvConfig;

/**
 * parent des modèles
 */
abstract class SaboModel{
    /**
     * connexion partagé entre les modèles
     */
    private static PDO $sharedCon;

    private static array $databaseConfig;

    /**
     * connexion du modèle
     */
    private PDO $myCon;

    public function __construct(?PDO $con = null){
        $this->myCon = $con != null ? $con : self::$sharedCon;
    }

    /**
     * initialise les modèles
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
        }
        else{
            if(!isset($configEnv["DB_HOST"],$configEnv["DB_USER"],$configEnv["DB_PASSWORD"],$configEnv["DB_NAME"]) ) throw new Exception("Missed a database key in configuration");

            self::$databaseConfig = [
                "host" => $configEnv["DB_HOST"],
                "user" => $configEnv["DB_USER"],
                "password" => $configEnv["DB_PASSWORD"],
                "name" => $configEnv["DB_NAME"]
            ];
        }

        // self::$sharedCon = self::getNewCon();
        echo "<pre>";
        var_dump(self::$databaseConfig);
        die();
        return true;
    }

    /**
     * crée une nouvelle connexion pdo
     * @return PDO|null la connexion ou null en cas d'échec
     * @throws Exception en mode debug en cas d'échec
     */
    public static function getNewCon():?PDO{
        $con = null;

        return $con;
    }
}