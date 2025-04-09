<?php

namespace Sabo\Utils\Routes;

/**
 * Route manager
 */
class RouteManager
{
    /**
     * @var Route[] Routes
     */
    protected array $routes = [];

    /**
     * @var RouteGroup[] Route groups
     */
    protected array $routeGroups = [];

    /**
     * Register a route
     * @param Route $route Route
     * @return $this
     */
    public function addRoute(Route $route):static
    {
        $method = $route->requestMethod;

        if(!array_key_exists(key: $method,array: $this->routes))
            $this->routes[$method] = [];

        $this->routes[$method][] = $route;

        return $this;
    }

    /**
     * Register a group
     * @param RouteGroup $group Route group
     * @return $this
     */
    public function addGroup(RouteGroup $group):static
    {
        $this->routeGroups[] = $group;

        return $this;
    }

    /**
     * @return Route[] Routes
     */
    public function getRoutes():array
    {
        return $this->routes;
    }

    /**
     * @return RouteGroup[] Route groups
     */
    public function getGroups():array
    {
        return $this->routeGroups;
    }
}