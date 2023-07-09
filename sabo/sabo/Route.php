<?php

namespace Sabo\Sabo;

use Sabo\Config\PathConfig;
use Sabo\Helper\Helper;
use Exception;
use Sabo\Middleware\Middleware\SaboMiddlewareCond;

/**
 * class utlitaire des routes
 */
abstract class Route{
    /**
     * routes du site
     */
    private static array $siteRoutes;

    /**
     * liste des méthodés acceptés
     */
    private const ACCEPTED_METHODS = ["get","post","put","delete"];

    /**
     * liste des noms de routes déjà utilisés
     */
    private static $usedNames = [];

    /**
     * groupe des routes ensemble
     * @param url url de préfixe, ne peut pas de paramètres générique 
     * @param routesGroup le groupe de routes à assembler
     * @param accessConds conditions d'accès supplémentaires à la page, (::class enfants de SaboMiddlewareCond,Closure booléenne ou callable booleen)
     * @return array les routes formatés
     */
    public static function group(string $url,array $routesGroup,array|callable $accessConds = []):array{
        foreach($routesGroup as $key => $group){
            if(!empty($group["urlRegex"]) ){
                $group["unmodifiedUrl"] = $url . $group["unmodifiedUrl"];
                $group["urlRegex"] = $url . $group["urlRegex"];
                $group["accessConds"] = array_merge($group["accessConds"],gettype($accessConds) != "array" ? [$accessConds] : $accessConds);
                $routesGroup[$key] = $group;
            }
            else $routesGroup[$key] = self::group($url,$group);
        }

        return $routesGroup;
    }

    /**
     * crée une route get
     * @param url le lien
     * @param toCall fonction ou tableau du format [Controller::class,"nom_methode"]
     * @param routeName nom de la route
     * @param paramsRegex pour une route générique tableau du format ["nom_variable" => "regex_associe"] ex: /article/{article_id} = ["article_id" => "[0-9]+"]
     * @param accessConds conditions d'accès supplémentaires à la page, (::class enfants de SaboMiddlewareCond,Closure booléenne ou callable booleen)
     * @return array les données de la route formatés pour le framework
     */
    public static function get(string $url,callable|array $toCall,string $routeName,array $paramsRegex = [],array|callable $accessConds = []):array{
        return self::createRouteFrom("get",$url,$toCall,$routeName,$paramsRegex,$accessConds);
    } 
    
    /**
     * crée une route post
     * @param url le lien
     * @param toCall fonction ou tableau du format [Controller::class,"nom_methode"]
     * @param routeName nom de la route
     * @param paramsRegex pour une route générique tableau du format ["nom_variable" => "regex_associe"] ex: /article/{article_id} = ["article_id" => "[0-9]+"]
     * @param accessConds conditions d'accès supplémentaires à la page, (::class enfants de SaboMiddlewareCond,Closure booléenne ou callable booleen)
     * @return array les données de la route formatés pour le framework
     */
    public static function post(string $url,callable|array $toCall,string $routeName,array $paramsRegex = [],array|callable $accessConds = []):array{
        return self::createRouteFrom("post",$url,$toCall,$routeName,$paramsRegex,$accessConds);
    } 

    /**
     * crée une route put
     * @param url le lien
     * @param toCall fonction ou tableau du format [Controller::class,"nom_methode"]
     * @param routeName nom de la route
     * @param paramsRegex pour une route générique tableau du format ["nom_variable" => "regex_associe"] ex: /article/{article_id} = ["article_id" => "[0-9]+"]
     * @param accessConds conditions d'accès supplémentaires à la page, (::class enfants de SaboMiddlewareCond,Closure booléenne ou callable booleen)
     * @return array les données de la route formatés pour le framework
     */
    public static function put(string $url,callable|array $toCall,string $routeName,array $paramsRegex = [],array|callable $accessConds = []):array{
        return self::createRouteFrom("put",$url,$toCall,$routeName,$paramsRegex,$accessConds);
    } 

    /**
     * crée une route delete
     * @param url le lien
     * @param toCall fonction ou tableau du format [Controller::class,"nom_methode"]
     * @param routeName nom de la route
     * @param paramsRegex pour une route générique tableau du format ["nom_variable" => "regex_associe"] ex: /article/{article_id} = ["article_id"
     * @param  => "[0-9]+"]
     * @param accessConds conditions d'accès supplémentaires à la page, (::class enfants de SaboMiddlewareCond,Closure booléenne ou callable booleen)
     * @return array les données de la route formatés pour le framework
     */
    public static function delete(string $url,callable|array $toCall,string $routeName,array $paramsRegex = [],array|callable $accessConds = []):array{
        return self::createRouteFrom("delete",$url,$toCall,$routeName,$paramsRegex,$accessConds);
    } 

    /**
     * formate les routes du site
     * @param routesData routes généré via les méthodes routes
     * @return array la liste des routes formattés
     */
    public static function generateFrom(array $routesData):array{
        $result = [
            "post" => [],
            "get" => [],
            "put" => [],
            "delete" => []
        ];

        // restructuration des routes
        foreach($routesData as $routeData){
            if(empty($routeData["method"]) ){
                $recResult = self::generateFrom($routeData);

                foreach($result as $method => $data) $result[$method] = array_merge($data,$recResult[$method]);
            }
            else array_push($result[$routeData["method"] ],$routeData);
        }   

        self::$siteRoutes = $result;

        return $result;
    }

    /**
     * récupère les routes à partir d'un fichier dans /config/routes/routes/
     * @param filename le nom du fichier contenant les routes
     * @return array les routes contenues dans le fichier
     */
    public static function getFromFile(string $filename):array{
        return Helper::require(PathConfig::ROUTES_SUBFOLER_PATH->value . "{$filename}.php");
    }

    /**
     * @return array les routes du site
     */
    public static function getSiteRoutes():array{
        return self::$siteRoutes;
    }

    /**
     * crée une route du type donnée
     * @param method la méthode de requête
     * @param url le lien
     * @param toCall fonction ou tableau du format [Controller::class,"nom_methode"]
     * @param routeName nom de la route unique
     * @param paramsRegex pour une route générique tableau du format ["nom_variable" => "regex_associe"] ex: /article/{article_id} = ["article_id" => "[0-9]+"] sinon .+ associé comme regex par défaut
     * @param accessConds conditions d'accès supplémentaires à la page, (::class enfants de SaboMiddlewareCond,Closure booléenne ou callable booleen)
     * @return array les données de la route formatés pour le framework
     * @throws Exception si la méthode n'est pas accepté ou que le nom de la route est déjà pris ou un nom de paramètre générique dupliqué, accessConds incorrect
     */
    private static function createRouteFrom(string $method,string $url,callable|array $toCall,string $routeName,array $paramsRegex,array|callable $accessConds):array{
        if(in_array($routeName,self::$usedNames) ) throw new Exception("Le nom de route {$routeName} est déjà utilisé");
        if(!in_array($method,self::ACCEPTED_METHODS) ) throw new Exception("La méthode {$method} n'est pas accepté sur la route nommé {$routeName}");

        array_push(self::$usedNames,$routeName);

        $paramsToSet = [];
        $toReplace = [];
        $replaces = [];

        $url = preg_quote($url);
        // redéfinition des valeurs non voulues
        $url = str_replace(["\{","\}","\-"],["{","}","-"],$url);

        // recherche des paramètres génériques
        @preg_match_all("#\{[a-zA-Z0-9\_]+\}#",$url,$genericParameters);

        if(!empty($genericParameters[0]) ) $toReplace = $genericParameters[0];

        // traitement des paramètres construction des regex
        foreach($toReplace as $genericParameter){
            // récupération du nom de variables sans {}
            $name = substr($genericParameter,1,-1); 

            if(in_array($name,$paramsToSet) ) throw new Exception("Le nom de paramètre générique {$genericParameter} est déjà utilisé, erreur sur la route {$url} -> {$routeName}");

            array_push($paramsToSet,$name);
            array_push($toReplace,$genericParameter);
            array_push($replaces,"(" . (array_key_exists($name,$paramsRegex) ? $paramsRegex[$name] : ".+") . ")");
        }

        $regex = str_replace($toReplace,$replaces,$url);

        $regex = "/{0,1}" . substr($regex,1);
        
        if(str_ends_with($regex,"/") ) $regex .= "{0,1}";

        $accessConds = gettype($accessConds) == "array" ? $accessConds : [$accessConds];

        // vérification de la validité des conditions
        foreach($accessConds as $accessCond){
            if((gettype($accessCond) == "string" && is_subclass_of($accessCond,SaboMiddlewareCond::class) ) || is_callable($accessCond) ) continue;


            throw new Exception("Une condition d'accès ne respecte pas le format donné");
        }

        return [
            "method" => $method,
            "paramsToSet" => $paramsToSet,
            "unmodifiedUrl" => $url,
            "urlRegex" => $regex,
            "toCall" => $toCall,
            "routeName" => $routeName,
            "accessConds" => $accessConds
        ];
    }
}