<?php

use SaboCore\Configuration\ApplicationConfiguration;
use SaboCore\Configuration\EnvConfig;
use SaboCore\Routing\Routes\Route;

# --------------------------------------------------------------------
# define your functions
# --------------------------------------------------------------------



# --------------------------------------------------------------------
# framework default global functions
# --------------------------------------------------------------------

/**
 * @param string $key database configuration env key
 * @return mixed the value or potentially null on key not found
 * @attention on production key not found warning is disabled
 */
function dbEnv(string $key):mixed{
    if(@$_ENV[EnvConfig::APPLICATION_CONFIGURATION][ApplicationConfiguration::APPLICATION_DEV_MODE])
        return $_ENV[EnvConfig::DATABASE_CONFIGURATION][$key];
    else
        return @$_ENV[EnvConfig::DATABASE_CONFIGURATION][$key];
}

/**
 * @param string $key maintenance configuration env key
 * @return mixed the value or potentially null on key not found
 * @attention on production key not found warning is disabled
 */
function maintenanceEnv(string $key):mixed{
    if(@$_ENV[EnvConfig::APPLICATION_CONFIGURATION][ApplicationConfiguration::APPLICATION_DEV_MODE])
        return $_ENV[EnvConfig::MAINTENANCE_CONFIGURATION][$key];
    else
        return @$_ENV[EnvConfig::MAINTENANCE_CONFIGURATION][$key];
}

/**
 * @param string $key mailer configuration env key
 * @return mixed the value or potentially null on key not found
 * @attention on production key not found warning is disabled
 */
function mailerEnv(string $key):mixed{
    if(@$_ENV[EnvConfig::APPLICATION_CONFIGURATION][ApplicationConfiguration::APPLICATION_DEV_MODE])
        return $_ENV[EnvConfig::MAILER_CONFIGURATION][$key];
    else
        return @$_ENV[EnvConfig::MAILER_CONFIGURATION][$key];
}

/**
 * @param string $key application configuration env key
 * @return mixed the value or potentially null on key not found
 * @attention on production key not found warning is disabled
 */
function appEnv(string $key):mixed{
    if(@$_ENV[EnvConfig::APPLICATION_CONFIGURATION][ApplicationConfiguration::APPLICATION_DEV_MODE])
        return $_ENV[EnvConfig::APPLICATION_CONFIGURATION][$key];
    else
        return @$_ENV[EnvConfig::APPLICATION_CONFIGURATION][$key];
}

/**
 * @param string $key custom configuration env key
 * @return mixed the value or potentially null on key not found
 * @attention on production key not found warning is disabled
 */
function customEnv(string $key):mixed{
    if(@$_ENV[EnvConfig::APPLICATION_CONFIGURATION][ApplicationConfiguration::APPLICATION_DEV_MODE])
        return $_ENV[EnvConfig::CUSTOM_CONFIGURATION][$key];
    else
        return @$_ENV[EnvConfig::CUSTOM_CONFIGURATION][$key];
}

/**
 * @brief build the link with the linked route name
 * @param string $routeName route name
 * @param array $replaces generic params to replace, indexed by the generic parameter name associated with the expected value
 * @return string the link or "/" if not found
 * @throws Exception on error
 * @attention if the application is in debug mode, "/" won't be returned, instead an exception will be thrown
 */
function getRoute(string $routeName,array $replaces = []):string{
    return buildRouteLink(requestMethod: "get",routeName: $routeName,replaces: $replaces);
}

/**
 * @brief build the link with the linked route name
 * @param string $routeName route name
 * @param array $replaces generic params to replace, indexed by the generic parameter name associated with the expected value
 * @return string the link or "/" if not found
 * @throws Exception on error
 * @attention if the application is in debug mode, "/" won't be returned, instead an exception will be thrown
 */
function postRoute(string $routeName,array $replaces = []):string{
    return buildRouteLink(requestMethod: "post",routeName: $routeName,replaces: $replaces);
}

/**
 * @brief build the link with the linked route name
 * @param string $routeName route name
 * @param array $replaces generic params to replace, indexed by the generic parameter name associated with the expected value
 * @return string the link or "/" if not found
 * @throws Exception on error
 * @attention if the application is in debug mode, "/" won't be returned, instead an exception will be thrown
 */
function putRoute(string $routeName,array $replaces = []):string{
    return buildRouteLink(requestMethod: "put",routeName: $routeName,replaces: $replaces);
}

/**
 * @brief build the link with the linked route name
 * @param string $routeName route name
 * @param array $replaces generic params to replace, indexed by the generic parameter name associated with the expected value
 * @return string the link or "/" if not found
 * @throws Exception on error
 * @attention if the application is in debug mode, "/" won't be returned, instead an exception will be thrown
 */
function deleteRoute(string $routeName,array $replaces = []):string{
    return buildRouteLink(requestMethod: "delete",routeName: $routeName,replaces: $replaces);
}

/**
 * @brief build the link with the linked route name
 * @param string $routeName route name
 * @param array $replaces generic params to replace, indexed by the generic parameter name associated with the expected value
 * @return string the link or "/" if not found
 * @throws Exception on error
 * @attention if the application is in debug mode, "/" won't be returned, instead an exception will be thrown
 */
function optionsRoute(string $routeName,array $replaces = []):string{
    return buildRouteLink(requestMethod: "options",routeName: $routeName,replaces: $replaces);
}

/**
 * @brief build the link with the linked route name
 * @param string $routeName route name
 * @param array $replaces generic params to replace, indexed by the generic parameter name associated with the expected value
 * @return string the link or "/" if not found
 * @throws Exception on error
 * @attention if the application is in debug mode, "/" won't be returned, instead an exception will be thrown
 */
function patchRoute(string $routeName,array $replaces = []):string{
    return buildRouteLink(requestMethod: "patch",routeName: $routeName,replaces: $replaces);
}

/**
 * @brief build the link with the linked route name
 * @param string $routeName route name
 * @param array $replaces generic params to replace, indexed by the generic parameter name associated with the expected value
 * @return string the link or "/" if not found
 * @throws Exception on error
 * @attention if the application is in debug mode, "/" won't be returned, instead an exception will be thrown
 */
function connectRoute(string $routeName,array $replaces = []):string{
    return buildRouteLink(requestMethod: "connect",routeName: $routeName,replaces: $replaces);
}

/**
 * @brief build the link with the linked route name
 * $@param string $requestMethod http request method
 * @param string $routeName route name
 * @param array $replaces generic params to replace, indexed by the generic parameter name associated with the expected value
 * @return string the link or "/" if not found
 * @throws Exception on error
 * @attention if the application is in debug mode, "/" won't be returned, instead an exception will be thrown
 */
function buildRouteLink(string $requestMethod,string $routeName,array $replaces):string{
    $isInDebug = appEnv(key: ApplicationConfiguration::APPLICATION_DEV_MODE);

    # check the request method existence
    if(!array_key_exists(key: $requestMethod,array: Route::$routes)){
        if($isInDebug)
            throw new Exception(message: "Unknown request method <$requestMethod> to get the route with name <$routeName>");
        else
            return "/";
    }

    if(!array_key_exists(key: $routeName,array: Route::$routes[$requestMethod])){
        if($isInDebug)
            throw new Exception(message: "Unknown route with name <$routeName> in the method <$requestMethod>");
        else
            return "/";
    }

    # replace elements
    $link = Route::$routes[$requestMethod][$routeName]->link;
    $genericParamsMatcher = Route::getGenericParamsMatchRegex();

    @preg_match_all(pattern: "#$genericParamsMatcher#",subject: $link,matches: $matches);

    if(!empty($matches[0]) && !empty($matches[1])){
        foreach($matches[0] as $key => $match){
            if(array_key_exists(key: $matches[1][$key],array: $replaces))
                $link = str_replace(search: $match,replace: $replaces[$matches[1][$key]],subject: $link);
        }
    }

    return $link;
}

