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
    ->setConfig(name: EnvConfig::APPLICATION_NAME_CONFIG->value,value: "Mon application")

    // lien de l'application
    ->setConfig(name: EnvConfig::APPLICATION_LINK_CONFIG->value,value: "http://127.0.0.1:8080/")

    // mode de développement true = dev - false = prod
    ->setConfig(name: EnvConfig::DEV_MODE_CONFIG->value,value: true)

    // configuration de l'état de maintenance
    ->setConfig(
        name: EnvConfig::MAINTENANCE_CONFIG->value,
        value: Config::create()
            ->setConfig(name: MaintenanceConfig::IS_IN_MAINTENANCE->value,value: false)
            ->setConfig(name: MaintenanceConfig::ACCESS_MANAGER->value,value: DefaultMaintenanceController::class)
            ->setConfig(name: MaintenanceConfig::SECRET_LINK->value,value: "/maintenance/dev/access/")
    )

    // configuration de la base de données
    ->setConfig(
        name: EnvConfig::DATABASE_CONFIG->value,
        value: Config::create()
            ->setConfig(name: DatabaseConfig::INIT_APP_WITH_CONNECTION->value,value: false)
            ->setConfig(name: DatabaseConfig::PROVIDER->value,value: new MysqlProvider() )
            ->setConfig(
                name: DatabaseConfig::PROVIDER_CONFIG->value,
                value: Config::create()
                    ->setConfig(name: "host",value: "localhost")
                    ->setConfig(name: "user",value: "root")
                    ->setConfig(name: "password",value: "")
                    ->setConfig(name: "dbname",value: "nom de la base de donnée")
            )
    )

    // configuration du mailer
    ->setConfig(
        name: EnvConfig::MAILER_CONFIG->value,
        // configuration vérifiée uniquement à l'usage de SaboMailer
        value: Config::create()
//            ->setConfig(name: MailerConfig::FROM_NAME->value,value: "")
//            ->setConfig(name: MailerConfig::FROM_EMAIL->value,value: "")
//            ->setConfig(name: MailerConfig::MAILER_PROVIDER_HOST->value,value: "smtp.gmail.com")
//            ->setConfig(name: MailerConfig::MAILER_PROVIDER_USERNAME->value,value: "")
//            ->setConfig(name: MailerConfig::MAILER_PROVIDER_PASSWORD->value,value: "")
//            ->setConfig(name: MailerConfig::PROVIDER_PORT->value,value: 465)
//            ->setConfig(name: MailerConfig::MAIL_TEMPLATES_DIR_PATH->value,value: "/src/views/mails")
    )

    // ajoutez vos propres configurations

    ;