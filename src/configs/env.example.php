<?php

use Controllers\DefaultMaintenanceController;
use SaboCore\Config\Config;
use SaboCore\Config\DatabaseConfig;
use SaboCore\Config\EnvConfig;
use SaboCore\Config\MailerConfig;
use SaboCore\Config\MaintenanceConfig;
use SaboCore\Database\Default\Provider\MysqlProvider;

/**
 * @brief Fichier d'environnement du framework
 * @return Config les variables d'environnement
 */
return Config::create()
    // configurations requises

    // nom de l'application
    ->setConfig(EnvConfig::APPLICATION_NAME_CONFIG->value,"Mon application")

    // lien de l'application
    ->setConfig(EnvConfig::APPLICATION_LINK_CONFIG->value,"http://127.0.0.1:8080/")

    // mode de développement true = dev - false = prod
    ->setConfig(EnvConfig::DEV_MODE_CONFIG->value,true)

    // configuration de l'état de maintenance
    ->setConfig(
        EnvConfig::MAINTENANCE_CONFIG->value,
        Config::create()
            ->setConfig(MaintenanceConfig::IS_IN_MAINTENANCE->value,false)
            ->setConfig(MaintenanceConfig::ACCESS_MANAGER->value,DefaultMaintenanceController::class)
            ->setConfig(MaintenanceConfig::SECRET_LINK->value,"/maintenance/secret/")
    )

    // configuration de la base de données
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
    )

    // configuration du mailer
    ->setConfig(
        EnvConfig::MAILER_CONFIG->value,
        // configuration vérifiée uniquement à l'usage de SaboMailer
        Config::create()
//            ->setConfig(MailerConfig::FROM_NAME->value,"")
//            ->setConfig(MailerConfig::FROM_EMAIL->value,"")
//            ->setConfig(MailerConfig::MAILER_PROVIDER_HOST->value,"smtp.gmail.com")
//            ->setConfig(MailerConfig::MAILER_PROVIDER_USERNAME->value,"")
//            ->setConfig(MailerConfig::MAILER_PROVIDER_PASSWORD->value,"")
//            ->setConfig(MailerConfig::MAIL_TEMPLATES_DIR_PATH->value,"/src/views/mails")
    )

    // ajoutez vos propres configurations

    ;