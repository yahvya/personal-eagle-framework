<?php

/**
 * @brief Entrypoint of the crontab handler's scripts. Require this script in every cron handler script
 */

$appRoot = __DIR__ . "/..";

require_once("$appRoot/vendor/autoload.php");

use Yahvya\EagleFramework\Config\Config;
use Yahvya\EagleFramework\Routing\Application\Application;

define(
    constant_name: "APP_CONFIG",
    value: Config::create()->setConfig(name: "ROOT", value: $appRoot)
);

Application::launchApplication(applicationConfig: Application::getApplicationDefaultConfig(), startRouting: false);