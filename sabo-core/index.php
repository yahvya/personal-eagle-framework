<?php session_start();

/**
 * @brief Point d'entrée du site
 * @author yahaya bathily https://github.com/yahvya/
 */

// inclusion de l'autoloader du framework ainsi que du client
$appRoot = __DIR__ . "/..";

require_once("$appRoot/sabo-core/vendor/autoload.php");
require_once("$appRoot/vendor/autoload.php");

use SaboCore\Config\ApplicationPathConfig;
use SaboCore\Config\Config;
use SaboCore\Routing\Application\Application;

// configuration publique de l'application
define(
    constant_name: "APP_CONFIG",
    value: Config::create()->setConfig(name: "ROOT", value: $appRoot)
);

// chargement des hooks et lancement de l'application
$applicationDefaultConfig = Application::getApplicationDefaultConfig(appRoot: $appRoot);

require_once($applicationDefaultConfig->getConfig(name: ApplicationPathConfig::HOOKS_CONFIG_FILEPATH->value));

Application::launchApplication(applicationConfig: $applicationDefaultConfig);