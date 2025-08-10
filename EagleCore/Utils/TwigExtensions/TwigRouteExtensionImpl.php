<?php

namespace Yahvya\EagleFramework\Utils\TwigExtensions;

/**
 * Trait, which provide the 'route' method
 */
trait TwigRouteExtensionImpl
{
    /**
     * @brief Search a route
     * @param string $requestMethod HTTP method
     * @param string $routeName Route name
     * @param array{string:string} $replaces Generic param replacements
     * @return string|null The route, associated link
     */
    public function route(string $requestMethod, string $routeName, array $replaces = []): string|null
    {
        return route(requestMethod: $requestMethod, routeName: $routeName, replaces: $replaces);
    }
}