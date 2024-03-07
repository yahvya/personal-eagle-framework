<?php

use SaboCore\Config\Config;
use SaboCore\Config\DatabaseConfig;
use SaboCore\Config\EnvConfig;
use SaboCore\Database\Providers\default\MysqlProvider;

/**
 * @brief Fichier d'environnement du framework
 * @return Config les variables d'environnement
 */
return Config::create()
    ->setConfig(EnvConfig::APPLICATION_NAME_CONFIG->value,"Sabo framework")
    ->setConfig(EnvConfig::APPLICATION_LINK_CONFIG->value,"https://sabo-final.local/")
    ->setConfig(
        EnvConfig::DATABASE_CONFIG->value,
        Config::create()
            ->setConfig(DatabaseConfig::INIT_APP_WITH_CONNECTION->value,true)
            ->setConfig(DatabaseConfig::PROVIDER->value,new MysqlProvider() )
            ->setConfig(
                DatabaseConfig::PROVIDER_CONFIG->value,
                Config::create()
                    ->setConfig("host","")
                    ->setConfig("user","")
                    ->setConfig("password","")
                    ->setConfig("dbname","")
            )
    );
    // ajoutez vos propres configurations
