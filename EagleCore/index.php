<?php session_start();

/**
 * @brief Application entrypoint
 */

$appRoot = __DIR__ . "/..";

require_once("$appRoot/vendor/autoload.php");

use Yahvya\EagleFramework\Config\Config;
use Yahvya\EagleFramework\Routing\Application\Application;

define(
    constant_name: "APP_CONFIG",
    value: Config::create()->setConfig(name: "ROOT", value: $appRoot)
);

Application::launchApplication(applicationConfig: Application::getApplicationDefaultConfig());