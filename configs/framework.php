<?php

use Yahvya\EagleFramework\Config\Config;
use Yahvya\EagleFramework\Config\FrameworkConfig;

/**
 * @brief Framework configuration file
 * @return Config Framework configuration
 */

// Place the configuration which should be loaded before the framework lifecycle gets too far there

date_default_timezone_set(timezoneId: "Europe/Paris");

return Config::create()
    // Required configuration
    ->setConfig(name: FrameworkConfig::PUBLIC_DIR_PATH->value, value: "/public")
    ->setConfig(name: FrameworkConfig::STORAGE_DIR_PATH->value, value: "/storage")
    ->setConfig(name: FrameworkConfig::ROUTES_BASEDIR_PATH->value, value: "/configs/routes")
    ->setConfig(name: FrameworkConfig::ROUTE_GENERIC_PARAMETER_MATCHER->value, value: "\:([a-zA-Z]+)")
    ->setConfig(name: FrameworkConfig::AUTHORIZED_EXTENSIONS_AS_PUBLIC->value, value: [".css", ".js"]);
