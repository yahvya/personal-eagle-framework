<?php

namespace Sabo\Sabo;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Exception;
use Sabo\Config\EnvConfig;
use Sabo\Config\PathConfig;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Controller\Controller\SaboController;
use Sabo\Helper\Helper;
use Sabo\Model\Model\SaboModel;

/**
 * routeur du framework
 */
abstract class Router{
    /**
     * démarre le site web
     * @return never la fonction arrête l'exécution
     * @throws Exception en mode débug en cas d'erreur
     */
    public static function initWebsite():never{
        $routes = Helper::require(PathConfig::MAIN_ROUTE_FILE->value);
        
        try{
            self::readEnv();
            self::initDatabase();
            SaboController::initControllers();
        }
        catch(Exception $e){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw $e;
            else
                call_user_func(SaboConfig::getCallableConfig(SaboConfigAttributes::TECHNICAL_ERROR_DEFAULT_PAGE) );
        }

        // vérification de l'état de maintenance
        if(SaboConfig::getBoolConfig(SaboConfigAttributes::MAINTENANCE_MODE) )
            call_user_func(SaboConfig::getCallableConfig(SaboConfigAttributes::MAINTENANCE_DEFAULT_PAGE) );

        $requestMethod = strtolower($_SERVER["REQUEST_METHOD"]);

        if(!array_key_exists($requestMethod,$routes) ){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw new Exception("Méthode de requête {$requestMethod} non accepté");
            else
                call_user_func(SaboConfig::getCallableConfig(SaboConfigAttributes::TECHNICAL_ERROR_DEFAULT_PAGE) );
        }

        // recherche de la route qui match
        foreach($routes[$requestMethod] as $routeData){
            if(@preg_match("#^{$routeData["urlRegex"]}$#",$_SERVER["PATH_INFO"],$matches) ){
                // vérification des conditions d'accès supplémentaires
                $areValid = true;

                foreach($routeData["accessConds"] as $condToCheck){
                    if(gettype($condToCheck) == "string"){
                        if(!$condToCheck::verify() ){
                            $condToCheck::toDoOnFail();

                            $areValid = false;

                            break;
                        }
                    }
                    else if(!call_user_func($condToCheck) ){
                        $areValid = false;

                        break;
                    }
                }

                if($areValid) self::startWithRouteData($routeData,$matches);
            }
        }

        call_user_func(SaboConfig::getCallableConfig(SaboConfigAttributes::NOT_FOUND_DEFAULT_PAGE) );
    }

    /**
     * lance le site à partir des données d'une route
     * @param routeData la route
     * @param regexMatches les données matchés de la regex de vérification
     * @return never la fonction arrête l'exécution
     * @throws Exception en mode debug throw les erreurs pouvant survenir
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

            if(array_key_exists($name,$paramsData) ) array_push($args,strlen($paramsData[$name]) == 0 ? null : $paramsData[$name]);
        }

        try{
            // lancement de la fonction
            call_user_func_array($toCall,$args);
        }
        catch(Exception $e){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) ) 
                throw $e;
            else
                call_user_func(SaboConfig::getCallableConfig(SaboConfigAttributes::TECHNICAL_ERROR_DEFAULT_PAGE) );
        }

        die();
    }   

    /**
     * lis le fichier de configuration
     */
    private static function readEnv():void{
        if(!EnvConfig::readEnv() ){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw new Exception("Echec de lecture du fichier de configuration");
            else 
                call_user_func(SaboConfig::getCallableConfig(SaboConfigAttributes::TECHNICAL_ERROR_DEFAULT_PAGE) );
        }
    }

    /**
     * initialise la base de données
     */
    private static function initDatabase():void{
        // initialisation de la base de données si configuré
        if(SaboConfig::getBoolConfig(SaboConfigAttributes::INIT_WITH_DATABASE_CONNEXION) ){
            // échec d'initialisation
            if(!SaboModel::initModel() ){
                // mode de test - exception renvoyée sinon affichage d'erreur technique
                if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) ) 
                    throw new Exception("Echec d'initilisation de la base de données");
                else 
                    call_user_func(SaboConfig::getCallableConfig(SaboConfigAttributes::TECHNICAL_ERROR_DEFAULT_PAGE) );

                die();
            }
        }
    }
}
