<?php

use Application\Controllers\DefaultMaintenanceController;
use Yahvya\EagleFramework\Config\Config;
use Yahvya\EagleFramework\Config\DatabaseConfig;
use Yahvya\EagleFramework\Config\EnvConfig;
use Yahvya\EagleFramework\Config\MaintenanceConfig;
use Yahvya\EagleFramework\Database\Default\Provider\MysqlProvider;

/**
 * @brief Application environment file
 * @return Config Environment variables
 */
return Config::create()
    // Required configurations

    // App name
    ->setConfig(name: EnvConfig::APPLICATION_NAME_CONFIG->value, value: "Eagle Framework")

    // Application base link
    ->setConfig(name: EnvConfig::APPLICATION_LINK_CONFIG->value, value: "http://127.0.0.1:8080/")

    // Development mode true = dev - false = prod|staging
    ->setConfig(name: EnvConfig::DEV_MODE_CONFIG->value, value: true)

    // Maintenance state configuration
    ->setConfig(
        name: EnvConfig::MAINTENANCE_CONFIG->value,
        value: Config::create()
            ->setConfig(name: MaintenanceConfig::IS_IN_MAINTENANCE->value, value: false)
            ->setConfig(name: MaintenanceConfig::ACCESS_MANAGER->value, value: DefaultMaintenanceController::class)
            ->setConfig(name: MaintenanceConfig::SECRET_LINK->value, value: "/maintenance/dev/access/")
    )

    // Database configuration
    ->setConfig(
        name: EnvConfig::DATABASE_CONFIG->value,
        value: Config::create()
            ->setConfig(name: DatabaseConfig::INIT_APP_WITH_CONNECTION->value, value: false)
            ->setConfig(name: DatabaseConfig::PROVIDER->value, value: new MysqlProvider())
            ->setConfig(
                name: DatabaseConfig::PROVIDER_CONFIG->value,
                value: Config::create()
                    ->setConfig(name: "host", value: "localhost")
                    ->setConfig(name: "user", value: "root")
                    ->setConfig(name: "password", value: "")
                    ->setConfig(name: "dbname", value: "<db_name>")
            )
    )

    // Mailer configuration
    ->setConfig(
        name: EnvConfig::MAILER_CONFIG->value,
        // If you use the internal 'EagleMailer' use the next lines
        value: Config::create()
    /*
        ->setConfig(name: MailerConfig::FROM_NAME->value,value: "")
        ->setConfig(name: MailerConfig::FROM_EMAIL->value,value: "")
        ->setConfig(name: MailerConfig::MAILER_PROVIDER_HOST->value,value: "smtp.gmail.com")
        ->setConfig(name: MailerConfig::MAILER_PROVIDER_USERNAME->value,value: "")
        ->setConfig(name: MailerConfig::MAILER_PROVIDER_PASSWORD->value,value: "")
        ->setConfig(name: MailerConfig::PROVIDER_PORT->value,value: 465)
        ->setConfig(name: MailerConfig::MAIL_TEMPLATES_DIR_PATH->value,value: "/src/views/mails")
    */
    )// Add your own environments there

    ;