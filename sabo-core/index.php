<?php session_start();

/**
 * @brief Point d'entrée du site
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
    "APP_CONFIG",
    Config::create()
        ->setConfig("ROOT",$appRoot)
);

// lancement de l'application
Application::launchApplication(
    Config::create()
        // configurations des chemins
        ->setConfig("ENV_CONFIG_FILEPATH","$appRoot/src/configs/env.php")
        ->setConfig("FUNCTIONS_CONFIG_FILEPATH","$appRoot/src/configs/functions.php")
        ->setConfig("FRAMEWORK_CONFIG_FILEPATH","$appRoot/src/configs/framework.php")
        ->setConfig("BLADE_FUNCTIONS_CONFIG_FILEPATH","$appRoot/src/configs/blade-config.php")
        ->setConfig("TWIG_FUNCTIONS_CONFIG_FILEPATH","$appRoot/src/configs/twig-config.php")
);