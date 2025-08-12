<?php

namespace Yahvya\EagleFramework\Routing\Routes;

use Closure;
use Yahvya\EagleFramework\Config\FrameworkConfig;
use Yahvya\EagleFramework\Routing\Application\Application;
use Yahvya\EagleFramework\Utils\Verification\Verifier;
use Throwable;

/**
 * @brief Application route
 */
class Route
{
    /**
     * @var string Request method (get, post, ...)
     */
    protected(set) string $requestMethod;

    /**
     * @var string Link
     */
    protected(set) string $link;

    /**
     * @var string Link in the form of a regular expression for comparison
     */
    protected(set) string $verificationRegex;

    /**
     * @var string Route name
     */
    protected(set) string $routeName;

    /**
     * @var array Regular expressions associated with generic parameters
     */
    protected(set) array $genericParamsRegex;

    /**
     * @var array Order of generic parameters in the request [order → name]
     */
    protected(set) array $genericParamsOrder = [];

    /**
     * @var Verifier[] Access verifiers for the route
     */
    protected(set) array $accessVerifiers;

    /**
     * @var Closure|array To execute to handle the route
     */
    protected(set) Closure|array $toExecute;

    /**
     * @param string $requestMethod Request method (get, post, ...)
     * @param string $link Link
     * @param Closure|array $toExecute To execute to handle the route
     * @param string $routeName Route name
     * @param array $genericParamsRegex Regular expressions associated with generic parameters
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     */
    public function __construct(string $requestMethod, string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = [])
    {
        if (!str_starts_with(haystack: $link, needle: "/"))
            $link = "/$link";

        if (!str_ends_with(haystack: $link, needle: "/"))
            $link = "$link/";

        $this->requestMethod = strtolower(string: $requestMethod);
        $this->link = $link;
        $this->toExecute = $toExecute;
        $this->routeName = $routeName;
        $this->genericParamsRegex = $genericParamsRegex;
        $this->accessVerifiers = $accessVerifiers;

        $this->updateConfig();
    }

    /**
     * @brief Adds a prefix to the link
     * @param string $prefix Prefix to add to the route
     * @param array $genericParameters Generic parameters to add to the route
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     * @return $this
     */
    public function addPrefix(string $prefix, array $genericParameters = [], array $accessVerifiers = []): Route
    {
        if (!str_starts_with(haystack: $prefix, needle: "/")) $prefix = "/$prefix";
        if (str_ends_with(haystack: $prefix, needle: "/")) $prefix = substr(string: $prefix, offset: 0, length: -1);

        $this->link = $prefix . $this->link;
        $this->genericParamsRegex = array_merge($this->genericParamsRegex, $genericParameters);
        $this->accessVerifiers = array_merge($this->accessVerifiers, $accessVerifiers);

        return $this->updateConfig();
    }

    /**
     * @brief Checks if the route matches the URL
     * @param string $url The URL
     * @return MatchResult The result of the match containing the association if matched
     */
    public function matchWith(string $url): MatchResult
    {
        @preg_match(pattern: "#^$this->verificationRegex$#", subject: $url, matches: $matches);

        if (empty($matches))
            return new MatchResult(haveMatch: false);

        // Associate retrieved parameters with their order
        $matchTable = [];

        unset($matches[0]);

        foreach ($matches as $key => $value)
            $matchTable[$this->genericParamsOrder[$key - 1]] = $value;

        return new MatchResult(haveMatch: true, matchTable: $matchTable);
    }

    /**
     * @brief Updates the route data based on the information in the link as well as the generic parameters
     * @return $this
     */
    protected function updateConfig(): Route
    {
        $this->verificationRegex = str_replace(search: "/", replace: "\/", subject: $this->link);
        $this->genericParamsOrder = [];
        $genericParameterMatcher = ":([a-zA-Z]+)";

        try
        {
            $genericParameterMatcher = Application::getFrameworkConfig()->getConfig(name: FrameworkConfig::ROUTE_GENERIC_PARAMETER_MATCHER->value);
        }
        catch (Throwable)
        {
        }

        @preg_match_all(pattern: "#$genericParameterMatcher#", subject: $this->link, matches: $matches);

        // Retrieve parameters
        foreach ($matches[0] as $key => $completeMatch)
        {
            $variableName = $matches[1][$key];

            // Save to the order array
            $this->genericParamsOrder[$key] = $variableName;

            // Replace in the string with regex
            $regex = $this->genericParamsRegex[$variableName] ?? "[^\/]+";
            $this->verificationRegex = str_replace(search: $completeMatch, replace: "($regex)", subject: $this->verificationRegex);
        }

        $this->verificationRegex .= "?";

        return $this;
    }

    /**
     * @brief Creates a GET route
     * @param string $link Link
     * @param Closure|array $toExecute To execute to handle the route
     * @param string $routeName Route name
     * @param array $genericParamsRegex Regular expressions associated with generic parameters
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     * @return Route The created route
     */
    public static function get(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route
    {
        return new Route(
            requestMethod: "get",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Creates a DELETE route
     * @param string $link Route link
     * @param Closure|array $toExecute Function to execute to handle the route
     * @param string $routeName Route name
     * @param array $genericParamsRegex Regular expressions associated with generic parameters
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     * @return Route The created route
     */
    public static function delete(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route
    {
        return new Route(
            requestMethod: "delete",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Creates a POST route
     * @param string $link Route link
     * @param Closure|array $toExecute Function to execute to handle the route
     * @param string $routeName Route name
     * @param array $genericParamsRegex Regular expressions associated with generic parameters
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     * @return Route The created route
     */
    public static function post(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route
    {
        return new Route(
            requestMethod: "post",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Creates a PUT route
     * @param string $link Route link
     * @param Closure|array $toExecute Function to execute to handle the route
     * @param string $routeName Route name
     * @param array $genericParamsRegex Regular expressions associated with generic parameters
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     * @return Route The created route
     */
    public static function put(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route
    {
        return new Route(
            requestMethod: "put",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Creates a PATCH route
     * @param string $link Route link
     * @param Closure|array $toExecute Function to execute to handle the route
     * @param string $routeName Route name
     * @param array $genericParamsRegex Regular expressions associated with generic parameters
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     * @return Route The created route
     */
    public static function patch(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route
    {
        return new Route(
            requestMethod: "patch",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Creates an OPTIONS route
     * @param string $link Route link
     * @param Closure|array $toExecute Function to execute to handle the route
     * @param string $routeName Route name
     * @param array $genericParamsRegex Regular expressions associated with generic parameters
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     * @return Route The created route
     */
    public static function options(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route
    {
        return new Route(
            requestMethod: "options",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Creates a HEAD route
     * @param string $link Route link
     * @param Closure|array $toExecute Function to execute to handle the route
     * @param string $routeName Route name
     * @param array $genericParamsRegex Regular expressions associated with generic parameters
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     * @return Route The created route
     */
    public static function head(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route
    {
        return new Route(
            requestMethod: "head",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }

    /**
     * @brief Creates a TRACE route
     * @param string $link Route link
     * @param Closure|array $toExecute Function to execute to handle the route
     * @param string $routeName Route name
     * @param array $genericParamsRegex Regular expressions associated with generic parameters
     * @param Verifier[] $accessVerifiers Access verifiers for the route, only failure functions are taken into account and return Response
     * @return Route The created route
     */
    public static function trace(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = []): Route
    {
        return new Route(
            requestMethod: "trace",
            link: $link,
            toExecute: $toExecute,
            routeName: $routeName,
            genericParamsRegex: $genericParamsRegex,
            accessVerifiers: $accessVerifiers
        );
    }
}
