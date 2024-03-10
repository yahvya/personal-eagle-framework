<?php

// routes web

use SaboCore\Routing\Response\BladeResponse;
use SaboCore\Routing\Routes\Route;
use SaboCore\Routing\Routes\RouteManager;

RouteManager::registerRoute(
    Route::get(
        link: "/",
        toExecute: fn():BladeResponse => new BladeResponse("sabo",["websiteLink" => "https://github.com/yahvya/sabo-final"]),
        routeName: "sabo"
    )
);
