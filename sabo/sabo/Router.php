<?php

namespace Sabo\Sabo;

use Sabo\Config\PathConfig;
use Sabo\Helper\Helper;

/**
 * routeur du framexork
 */
abstract class Router{
    /**
     * démarre le site web
     */
    public static function initWebsite():void{
        $routes = Helper::require(PathConfig::MAIN_ROUTE_FILE->value);

        echo "<pre>";
        var_dump($routes);
        // die($_SERVER["PATH_INFO"]);

        die();
    }
}