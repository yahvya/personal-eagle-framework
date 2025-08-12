<?php

namespace Yahvya\EagleFramework\Routing\Routes;

use Closure;
use Yahvya\EagleFramework\Config\ConfigException;
use Yahvya\EagleFramework\Config\EnvConfig;
use Yahvya\EagleFramework\Config\FrameworkConfig;
use Yahvya\EagleFramework\Routing\Application\Application;
use Yahvya\EagleFramework\Utils\Verification\Verifier;
use Throwable;

/**
 * @brief Route manager
 */
abstract class RouteManager
{
    /**
     * @var array{string:Route[]} site links
     */
    protected static array $routes = [];

    /**
     * @var string[] used route names
     */
    protected static array $usedNames = [];

    /**
     * @brief Registers a group of routes
     * @param string $linksPrefix prefix for the links contained in the group
     * @param Route[] $routes list of routes in the group
     * @param array $genericParamsConfig regular expressions linked to generic elements (applied to all links in the group)
     * @param Verifier[] $groupAccessVerifiers access handlers (applied to all links in the group), receive a Request object as a parameter; only failure functions are taken into account and return a Response
     * @return void
     */
    public static function registerGroup(string $linksPrefix, array $routes, array $genericParamsConfig = [], array $groupAccessVerifiers = []): void
    {
        foreach ($routes as $route)
        {
            $route->addPrefix(prefix: $linksPrefix, genericParameters: $genericParamsConfig, accessVerifiers: $groupAccessVerifiers);
            self::registerRoute(
                requestMethod: $route->requestMethod,
                link: $route->link,
                toExecute: $route->toExecute,
                routeName: $route->routeName,
                genericParamsRegex: $route->genericParamsRegex,
                accessVerifiers: $route->accessVerifiers
            );
        }
    }

    /**
     * @brief Registers a route
     * @param string $requestMethod Request method (get, post, ...)
     * @param string $link Link
     * @param Closure|array $toExecute To execute to handle the route
     * @param string $routeName Route name
     * @param array $genericParamsRegex Regular expressions associated with generic parameters
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     * @return void
     */
    public static function registerRoute(string $requestMethod, string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): void
    {
        $route = new Route(
            requestMethod: $requestMethod,
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );

        $routeName = $route->routeName;

        try
        {
            $isDebugMode = Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value);

            if (in_array($routeName, self::$usedNames))
            {
                if ($isDebugMode)
                    debugDie("The route name $routeName is already used");
            }
            else
            {
                $method = $route->requestMethod;

                // save the used name
                self::$usedNames[] = $routeName;

                // register the route
                if (!array_key_exists(key: $method, array: self::$routes)) self::$routes[$method] = [];

                self::$routes[$method][] = $route;
            }
        }
        catch (ConfigException)
        {
        }
    }

    /**
     * @brief Loads routes written in a file
     * @warning the search is done from the root routes folder
     * @param string $filename filename without the php extension
     * @return void
     */
    public static function fromFile(string $filename): void
    {
        try
        {
            $path = APP_CONFIG->getConfig(name: "ROOT") . Application::getFrameworkConfig()->getConfig(name: FrameworkConfig::ROUTES_BASEDIR_PATH->value) . "/$filename.php";

            if (@file_exists(filename: $path)) require_once($path);
        }
        catch (Throwable)
        {
        }
    }

    /**
     * @brief Searches for a route by its name
     * @param string $routeName the route name
     * @param string|null $method the request method
     * @return Route|null the route or null
     */
    public static function findRouteByName(string $routeName, ?string $method = null): ?Route
    {
        $routes = self::getRoutesFrom(method: $method);

        return array_find($routes, fn($route) => $route->routeName === $routeName);
    }

    /**
     * @brief Searches for a route by matching a link
     * @param string $link the link
     * @param string|null $method the request method
     * @return array<string,Route|MatchResult>|null null if not found otherwise ["route" => ..., "match" => ...]
     */
    public static function findRouteByLink(string $link, ?string $method = null): ?array
    {
        $routes = self::getRoutesFrom(method: $method);

        foreach ($routes as $route)
        {
            // search route by match
            $match = $route->matchWith(url: $link);

            if ($match->getHaveMatch()) return ["route" => $route, "match" => $match];
        }

        return null;
    }

    /**
     * @brief Forms routes from the method
     * @param string|null $method the method
     * @return Route[]
     */
    protected static function getRoutesFrom(?string $method): array
    {
        if ($method !== null && array_key_exists(key: $method, array: self::$routes))
            $routes = self::$routes[strtolower(string: $method)];
        else if ($method === null)
            $routes = array_merge(...array_values(array: self::$routes));
        else
            $routes = [];

        return $routes;
    }
}
