<?php

namespace SaboCore\Core\Http;

/**
 * Route manager
 */
class RouteManager
{
    /**
     * @var Route[] Routes list
     */
    protected array $routes = [];

    /**
     * @var string[] Allowed domains
     */
    protected array $corsAllowedDomains = [];

    /**
     * Register a new route
     * @param Route $route Route
     * @return $this
     */
    public function addRoute(Route $route):RouteManager
    {
        $this->routes[] = $route;
        return $this;
    }

    /**
     * Modify the allowed domains in cors
     * @param array $corsAllowedDomains Cors allowed domains
     * @return $this
     */
    public function setCorsAllowedDomains(array $corsAllowedDomains):static
    {
        $this->corsAllowedDomains = $corsAllowedDomains;

        return $this;
    }
}