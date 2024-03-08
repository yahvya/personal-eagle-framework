<?php

use SaboCore\Config\Config;
use SaboCore\Config\FrameworkConfig;

/**
 * @brief Fichier de configuration global du framework
 * @return Config les variables d'environnement
 */

// placez ici les configurations globales

return Config::create()
    // configurations requises
    ->setConfig(FrameworkConfig::PUBLIC_DIR_PATH->value,"/src/public")
    ->setConfig(FrameworkConfig::STORAGE_DIR_PATH->value,"/src/storage")
    ->setConfig(FrameworkConfig::ROUTES_BASEDIR_PATH->value,"/src/routes")
    ->setConfig(FrameworkConfig::ROUTE_GENERIC_PARAMETER_MATCHER->value,":([a-zA-Z]+)")
    ->setConfig(FrameworkConfig::AUTHORIZED_EXTENSIONS_AS_PUBLIC->value,[".css",".js"]);
