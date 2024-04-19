<?php

namespace SaboCore\Database\Default\Provider;

use Override;
use PDO;
use SaboCore\Config\Config;
use SaboCore\Config\ConfigException;
use SaboCore\Database\Providers\DatabaseProvider;
use Throwable;

/**
 * @brief Fournisseur mysql
 * @author yahaya bathily https://github.com/yahvya/
 */
class MysqlProvider extends DatabaseProvider{
    /**
     * @var PDO|null instance partagée de connexion à la base de données
     */
    protected static ?PDO $con;

    #[Override]
    public function initDatabase(Config $providerConfig):void{
        // vérification de la configuration mysql
        $providerConfig->checkConfigs("host","user","password","dbname");

        try{
            self::$con = new PDO(
                dsn: "mysql:host={$providerConfig->getConfig(name: "host")};dbname={$providerConfig->getConfig(name: "dbname")}",
                username: $providerConfig->getConfig(name: "user"),
                password: $providerConfig->getConfig(name: "password"),
                options: [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        catch(Throwable){
            throw new ConfigException(message: "Echec de connexion à la base de donnée");
        }
    }

    /**
     * @return PDO|null la connexion crée à l'initialisation ou null
     */
    #[Override]
    public function getCon():?PDO{
        return self::$con;
    }
}