<?php

namespace SaboCore\Routing\Routes;

use SaboCore\Config\FrameworkConfig;
use SaboCore\Routing\Application\Application;
use Throwable;

/**
 * @brief Gestionnaire de routes
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class RouteManager{
    /**
     * @var array liens du site
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
     * @param AccessVerifier[] $groupAccessVerifiers gestionnaires d'accès (appliquées à tous les liens du groupe), reçoivent un objet Request en paramètre
     * @return void
     */
    public static function registerGroup(string $linksPrefix,array $routes,array $genericParamsConfig = [],array $groupAccessVerifiers = []):void{
        foreach($routes as $route){
            $route->addPrefix($linksPrefix,$genericParamsConfig,$groupAccessVerifiers);
            self::registerRoute($route);
        }
    }

    /**
     * @brief Enregistre une route
     * @param Route $route la route
     * @return void
     */
    public static function registerRoute(Route $route):void{

    }

    /**
     * @brief Charge les routes écrites dans un fichier
     * @attention la recherche se fait à partir du dossier racine des routes
     * @param string $filename nom du fichier sans l'extension php
     * @return void
     */
    public static function fromFile(string $filename):void{
        try{
            $path = APP_CONFIG->getConfig("ROOT") . Application::getFrameworkConfig()->getConfig(FrameworkConfig::ROUTES_BASEDIR_PATH->value) . "/$filename.php";

            if(file_exists($path) ) require_once($path);
        }
        catch(Throwable){}
    }
}