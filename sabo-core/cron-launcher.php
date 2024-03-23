<?php session_start();

/**
 * @brief Point d'entrée de configuration pour scripts cron
 * @author yahaya bathily https://github.com/yahvya/
 */

// inclusion de l'autoloader du framework ainsi que du client
$appRoot = __DIR__ . "/..";

require_once("$appRoot/sabo-core/vendor/autoload.php");
require_once("$appRoot/vendor/autoload.php");

use SaboCore\Config\Config;
use SaboCore\Routing\Application\Application;

// configuration publique de l'application
define(
    constant_name: "APP_CONFIG",
    value: Config::create()->setConfig(name: "ROOT", value: $appRoot)
);

// lancement de l'application
Application::launchApplication(applicationConfig: Application::getApplicationDefaultConfig(),startRouting: false);