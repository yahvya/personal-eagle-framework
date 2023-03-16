<?php

namespace Sabo\Sabo;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Sabo\Config\PathConfig;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Helper\Helper;

/**
 * routeur du framexork
 */
abstract class Router{
    /**
     * démarre le site web
     * @return never la fonction arrête l'exécution
     */
    public static function initWebsite():never{
        $routes = Helper::require(PathConfig::MAIN_ROUTE_FILE->value);

        $requestMethod = strtolower($_SERVER["REQUEST_METHOD"]);

        if(!array_key_exists($requestMethod,$routes) ){
            die("methode non accepté");
        }

        // recherche de la route qui match
        foreach($routes[$requestMethod] as $routeData){
            if(@preg_match("#^{$routeData["urlRegex"]}$#",$_SERVER["PATH_INFO"],$matches) ) self::startWithRouteData($routeData,$matches);
        }

        call_user_func(SaboConfig::getCallableConfig(SaboConfigAttributes::NO_FOUND_DEFAULT_PAGE) );
    }

    /**
     * lance le site à partir des données d'une route
     * @param routeData la route
     * @param regexMatches les données matchés de la regex de vérification
     * @return never la fonction arrête l'exécution
     */
    private static function startWithRouteData(array $routeData,array $regexMatches):never{

        list("paramsToSet" => $paramsToSet,"toCall" => $toCall) = $routeData;

        $args = [];

        unset($regexMatches[0]);

        $regexMatches = array_values($regexMatches);

        $paramsData = [];

        // affectation des paramètres possible de la fonction à appeller
        foreach($regexMatches as $key => $match) $paramsData[$paramsToSet[$key] ] = $match; 

        // alors méthode provenant d'un controller sinon fonction
        if(gettype($toCall) == "array"){
            $reflectionClass = new ReflectionClass($toCall[0]);

            // création d'une instance du controller
            $toCall[0] = $reflectionClass->newInstance();

            $reflection = new ReflectionMethod($toCall[0],$toCall[1]);
        }
        else $reflection = new ReflectionFunction($toCall);

        // récupération des paramètres de la fonction
        foreach($reflection->getParameters() as $reflectionParam){
            $name = $reflectionParam->getName();

            if(array_key_exists($name,$paramsData) ) array_push($args,$paramsData[$name]);
        }

        call_user_func_array($toCall,$args);

        die();
    }
}