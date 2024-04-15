<?php

namespace SaboCore\Routing\Routes;

use SaboCore\Config\ConfigException;
use SaboCore\Config\EnvConfig;
use SaboCore\Config\FrameworkConfig;
use SaboCore\Routing\Application\Application;
use SaboCore\Utils\Verification\Verifier;
use Throwable;

/**
 * @brief Gestionnaire de routes
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class RouteManager{
    /**
     * @var array<string,Route[]> liens du site
     */
    protected static array $routes = [];

    /**
     * @var string[] noms de routes utilisés
     */
    protected static array $usedNames = [];

    /**
     * @brief Enregistre un groupe de route
     * @param string $linksPrefix prefix des liens contenus dans le groupe
     * @param Route[] $routes liste des routes du groupe
     * @param array $genericParamsConfig expressions régulières liées aux éléments génériques (appliquées à tous les liens du groupe)
     * @param Verifier[] $groupAccessVerifiers gestionnaires d'accès (appliquées à tous les liens du groupe), reçoivent un objet Request en paramètre les fonctions, seuls les fonctions failures sont prises en compte et retournent Response
     * @return void
     */
    public static function registerGroup(string $linksPrefix,array $routes,array $genericParamsConfig = [],array $groupAccessVerifiers = []):void{
        foreach($routes as $route){
            $route->addPrefix(prefix: $linksPrefix,genericParameters: $genericParamsConfig,accessVerifiers: $groupAccessVerifiers);
            self::registerRoute(route: $route);
        }
    }

    /**
     * @brief Enregistre une route
     * @param Route $route la route
     * @return void
     */
    public static function registerRoute(Route $route):void{
        $routeName = $route->getRouteName();

        try{
            $isDebugMode = Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value);

            if(in_array($routeName,self::$usedNames) ){
                if($isDebugMode)
                    debugDie("Le nom de route $routeName est déjà utilisé");
            }
            else{
                $method = $route->getRequestMethod();

                // sauvegarde du nom utilisé
                self::$usedNames[] = $routeName;

                // enregistrement de la route
                if(!array_key_exists(key: $method,array: self::$routes) ) self::$routes[$method] = [];

                self::$routes[$method][] = $route;
            }
        }
        catch(ConfigException){}
    }

    /**
     * @brief Charge les routes écrites dans un fichier
     * @attention la recherche se fait à partir du dossier racine des routes
     * @param string $filename nom du fichier sans l'extension php
     * @return void
     */
    public static function fromFile(string $filename):void{
        try{
            $path = APP_CONFIG->getConfig(name: "ROOT") . Application::getFrameworkConfig()->getConfig(name: FrameworkConfig::ROUTES_BASEDIR_PATH->value) . "/$filename.php";

            if(@file_exists(filename: $path) ) require_once($path);
        }
        catch(Throwable){}
    }

    /**
     * @brief Recherche une route par son nom
     * @param string $routeName nom de la route
     * @param string|null $method la méthode de requête
     * @return Route|null la route ou null
     */
    public static function findRouteByName(string $routeName,?string $method = null):?Route{
        $routes = self::getRoutesFrom(method: $method);

        foreach($routes as $route){
            if($route->getRouteName() === $routeName)
                return $route;
        }

        return null;
    }

    /**
     * @brief Recherche une route par match de lien
     * @param string $link lien
     * @param string|null $method la méthode de requête
     * @return array<string,Route|MatchResult>|null null si non trouvé sinon ["route" => ...,"match" => ...]
     */
    public static function findRouteByLink(string $link,?string $method = null):?array{
        $routes = self::getRoutesFrom(method: $method);

        foreach ($routes as $route){
            // recherche de la route par match
            $match = $route->matchWith(url: $link);

            if($match->getHaveMatch() ) return ["route" => $route,"match" => $match];
        }

        return null;
    }

    /**
     * @brief Forme les routes à partir de la méthode
     * @param string|null $method la méthode
     * @return Route[]
     */
    protected static function getRoutesFrom(?string $method):array{
        if($method !== null && array_key_exists(key: $method,array: self::$routes) )
            $routes = self::$routes[strtolower(string: $method)];
        else if($method === null)
            $routes = array_merge(...array_values(array: self::$routes) );
        else
            $routes = [];

        return $routes;
    }
}