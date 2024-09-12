<?php

namespace SaboCore\Routing\Routes;

use Closure;
use Exception;
use SaboCore\Utils\CustomTypes\Map;

/**
 * @brief app route
 * @default generic params format match regex : \:([a-Z_A-Z]+)
 * @method static Route get(string $link,Callable $executor,string $routeName)
 * @method static Route post(string $link,Callable $executor,string $routeName)
 * @method static Route put(string $link,Callable $executor,string $routeName)
 * @method static Route delete(string $link,Callable $executor,string $routeName)
 * @method static Route head(string $link,Callable $executor,string $routeName)
 * @method static Route options(string $link,Callable $executor,string $routeName)
 * @method static Route patch(string $link,Callable $executor,string $routeName)
 * @method static Route connect(string $link,Callable $executor,string $routeName)
 */
class Route{
    /**
     * @const accepted request methods
     */
    public const array ACCEPTED_METHODS = ["get","post","put","delete","head","options","patch","connect"];

    /**
     * @var string generic params format match regex
     */
    protected static string $genericParamsFormatMatcher = "\:([a-Z_A-Z]+)";

    /**
     * @var Array<string,Route> routes list indexed by the http method
     */
    public static array $routes = [];

    /**
     * @param string $link route link (can contain generic params)
     * @param Closure|array $executor callable (controller:method) or a closure which return a RouteResponse
     * @param string $routeName route name , two route can't have the same name if they are in the same requestMethod
     * @param Map $genericParamsCustomRegex custom regex to associate to route (the default associated regex is in UrlMatcher)
     * @param (Callable)[] $accessConditions access conditions, callables which return boolean, true to pass, false to block
     */
    protected function __construct(
        public readonly string $link,
        public readonly Closure|array $executor,
        public readonly string $routeName,
        public readonly Map $genericParamsCustomRegex = new Map(),
        public array $accessConditions = []
    ){
    }

    /**
     * @brief set a generic param custom regex
     * @param string $paramName generic param name
     * @param string $regex custom regex
     * @return $this
     */
    public function setGenericParamRegex(string $paramName,string $regex):static{
        $this->genericParamsCustomRegex->set(key: $paramName,value: $regex);

        return $this;
    }

    /**
     * @brief add a new access cond
     * @param Closure|array $cond condition
     * @return $this
     */
    public function addAccessCond(Closure|array $cond):static{
        $this->accessConditions[] = $cond;

        return $this;
    }

    /**
     * @param string $name method name
     * @param array $arguments register arguments
     * @return Route generate route
     * @throws Exception in case of an unaccepted method
     */
    public static function __callStatic(string $name, array $arguments):Route{
        if(!in_array(needle: $name,haystack: static::ACCEPTED_METHODS))
            throw new Exception(message: "Unaccepted method <$name>");

        $orderedArguments = isset($arguments["link"]) ?
            [
                $arguments["link"],
                $arguments["executor"],
                $arguments["routeName"],
            ] :
            array_values(array: $arguments);

        return static::registerRoute($name,...$orderedArguments);
    }

    /**
     * @brief set the generic params format match regex
     * @param string $regex regex
     * @return void
     */
    public static function setGenericParamsMatchRegex(string $regex):void{
        static::$genericParamsFormatMatcher = $regex;
    }

    /**
     * @return string the generic params format match regex
     */
    public static function getGenericParamsMatchRegex():string{
        return static::$genericParamsFormatMatcher;
    }

    /**
     * @brief register a new route after applying some verifications
     * @param string $link route link (can contain generic params)
     * @param Callable $executor callable (controller:method) or a closure which return a RouteResponse
     * @param string $routeName route name , two route can't have the same name if they are in the same requestMethod
     * @return Route the created route
     * @throws Exception on route exists
     */
    protected static function registerRoute(
        string $requestMethod,
        string $link,
        Callable $executor,
        string $routeName,
    ):Route{
        if(array_key_exists(key: $routeName,array: static::$routes[$requestMethod] ?? []))
            throw new Exception(message: "A route with the name <$routeName> already exists in the <$requestMethod> Http Method - Link : $link");

        $route = new Route(
            link: $link,
            executor: $executor,
            routeName: $routeName
        );

        static::$routes[$requestMethod][$routeName] = $route;

        return $route;
    }
}