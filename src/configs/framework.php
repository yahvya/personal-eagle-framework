<?php

use SaboCore\Config\Config;
use SaboCore\Config\FrameworkConfig;

/**
 * @brief Fichier de configuration global du framework
 * @return Config les variables d'environnement
 */

// placez ici les configurations globales

date_default_timezone_set(timezoneId: "Europe/Paris");

return Config::create()
    // configurations requises
    ->setConfig(name: FrameworkConfig::PUBLIC_DIR_PATH->value,value: "/src/public")
    ->setConfig(name: FrameworkConfig::STORAGE_DIR_PATH->value,value: "/src/storage")
    ->setConfig(name: FrameworkConfig::ROUTES_BASEDIR_PATH->value,value: "/src/routes")
    ->setConfig(name: FrameworkConfig::ROUTE_GENERIC_PARAMETER_MATCHER->value,value: "\:([a-zA-Z]+)")
    ->setConfig(name: FrameworkConfig::AUTHORIZED_EXTENSIONS_AS_PUBLIC->value,value: [".css",".js"]);
