<?php

namespace SaboCore\Database\Providers\Default;

use Override;
use PDO;
use SaboCore\Config\Config;
use SaboCore\Config\ConfigException;
use SaboCore\Database\Providers\Providers\DatabaseProvider;
use Throwable;

/**
 * @brief Fournisseur mysql
 * @author yahaya bathily https://github.com/yahvya/
 */
class MysqlProvider extends DatabaseProvider{
    /**
     * @var PDO|null instance partagée de connexion à la base de données
     */
    private static ?PDO $con;

    #[Override]
    public function initDatabase(Config $providerConfig):void{
        // vérification de la configuration mysql
        $providerConfig->checkConfigs("host","user","password","dbname");

        try{
            self::$con = new PDO(
                "mysql:host={$providerConfig->getConfig("host")};dbname={$providerConfig->getConfig("dbname")}",
                $providerConfig->getConfig("user"),
                $providerConfig->getConfig("password"),[
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        catch(Throwable){
            throw new ConfigException("Echec de connexion à la base de donnée");
        }
    }

    /**
     * @return PDO|null la connexion crée à l'initialisation ou null
     */
    public static function getCon():?PDO{
        return self::$con;
    }
}