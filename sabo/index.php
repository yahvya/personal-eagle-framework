<?php session_start();

use Sabo\Config\PathConfig;
use Sabo\Config\SaboConfig;
use Sabo\Helper\FileHelper;
use Sabo\Helper\Helper;
use Sabo\Sabo\Router;

define("ROOT",__DIR__ . "\\..\\");

require_once("./vendor/autoload.php");

// définition des configurations par défaut
SaboConfig::setDefaultConfigurations();

// inclusion des configurations utilisateur
Helper::require(PathConfig::SABO_CONFIG_FILEPATH->value);

// inclusion de l'autoloader utilisateur
if(FileHelper::fileExist(PathConfig::USER_AUTOLOAD_FILEPATH->value) ) Helper::require(PathConfig::USER_AUTOLOAD_FILEPATH->value);

// démarrage du site
Router::initWebsite();