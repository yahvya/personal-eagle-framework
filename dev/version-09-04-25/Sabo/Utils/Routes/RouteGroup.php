<?php

namespace Sabo\Utils\Routes;

/**
 * Group of route
 */
class RouteGroup
{
    /**
     * @var Route[] child routes
     */
    protected array $containedRoutes;

    /**
     * @param string $prefixLink Sub routes prefix links. Can contain dynamic params.
     * @attention The prefix must start with /
     */
    public function __construct(
        public readonly string $prefixLink
    )
    {
        $this->containedRoutes = [];
    }

    /**
     * Add route in the group
     * @param Route $route route
     * @return $this
     */
    public function add(Route $route):static
    {
        $this->containedRoutes[] = $route;

        return $this;
    }

    /**
     * @return Route[] route list
     */
    public function getRoutes():array
    {
        return $this->containedRoutes;
    }

    /**
     * @return Route[] formated route list with the prefix
     */
    public function getFormatedRoutes():array
    {
        return array_map(
            callback: fn(Route $route):Route => $route->addPrefix(prefix: $this->prefixLink),
            array: $this->containedRoutes
        );
    }
}