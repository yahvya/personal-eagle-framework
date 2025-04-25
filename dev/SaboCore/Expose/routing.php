<?php

// ROUTING UTILS

use SaboCore\Core\Http\RequestManager;
use SaboCore\Core\Http\Route;
use SaboCore\Core\Http\RouteManager;

/**
 * @return RequestManager A singleton request manager
 */
function request():RequestManager
{
    static $requestManager = new RequestManager();

    return $requestManager;
}

/**
 * @return RouteManager A singleton route manager
 */
function routeManager():RouteManager
{
    static $routeManager = new RouteManager();

    return $routeManager;
}

/**
 * @return Route New registered route to fill
 */
function route():Route
{
    $routeManager = routeManager();

    $route = new Route(routeManager: $routeManager);
    $routeManager->addRoute(route: $route);

    return $route;
}