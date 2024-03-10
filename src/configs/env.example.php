<?php

use Controllers\DefaultMaintenanceController;
use SaboCore\Config\Config;
use SaboCore\Config\DatabaseConfig;
use SaboCore\Config\EnvConfig;
use SaboCore\Config\MaintenanceConfig;
use SaboCore\Database\Default\provider\MysqlProvider;

/**
 * @brief Fichier d'environnement du framework
 * @return Config les variables d'environnement
 */
return Config::create()
    // configurations requises
    ->setConfig(EnvConfig::APPLICATION_NAME_CONFIG->value,"Sabo framework")
    ->setConfig(EnvConfig::APPLICATION_LINK_CONFIG->value,"https://sabo-final.local/")
    ->setConfig(EnvConfig::DEV_MODE_CONFIG->value,true)
    ->setConfig(
        EnvConfig::MAINTENANCE_CONFIG->value,
        Config::create()
            ->setConfig(MaintenanceConfig::IS_IN_MAINTENANCE->value,false)
            ->setConfig(MaintenanceConfig::ACCESS_MANAGER->value,DefaultMaintenanceController::class)
            ->setConfig(MaintenanceConfig::SECRET_LINK->value,"/maintenance/access/mkjlhgcfnbvhjklicvhjgv")
    )
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
