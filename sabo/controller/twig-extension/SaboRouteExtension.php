<?php

namespace Sabo\Controller\TwigExtension;

use Exception;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Sabo\Sabo\Route;
use Twig\TwigFunction;

/**
 * extension de récupération des routes
 * fonctions [getRoute,postRoute,putRoute,deleteRoute,jRoute]
 */
class SaboRouteExtension extends SaboExtension{
    private static array $routes = [];

    public function getFunctions():array{
        return [
            new TwigFunction("getRoute", [$this,"getRoute"]),
            new TwigFunction("postRoute", [$this,"postRoute"]),
            new TwigFunction("putRoute", [$this,"putRoute"]),
            new TwigFunction("deleteRoute", [$this,"deleteRoute"]),
            new TwigFunction("jRoute",[$this,"jRoute"],["is_safe" => ["html" => true] ])
        ];
    }


    /**
     * @param routeName le nom de la route 
     * @param routeParams tableau associatif des paramètres optionneles du lien [nom => valeur]
     * @return string le lien lié à la route ou un lien / en cas de route non trouvé [cas get]
     * @throws Exception en mode debug si la route n'existe pas
     */
    public function getRoute(string $routeName,array $routeParams = []):string{
        return $this->routeFrom(self::$routes["get"],$routeName,$routeParams);
    } 
    
    /**
     * @param routeName le nom de la route 
     * @param routeParams tableau associatif des paramètres optionneles du lien [nom => valeur]
     * @return string le lien lié à la route ou un lien / en cas de route non trouvé [cas post]
     * @throws Exception en mode debug si la route n'existe pas
     */
    public function postRoute(string $routeName,array $routeParams = []):string{
        return $this->routeFrom(self::$routes["post"],$routeName,$routeParams);
    } 

    /**
     * @param routeName le nom de la route 
     * @param routeParams tableau associatif des paramètres optionneles du lien [nom => valeur]
     * @return string le lien lié à la route ou un lien / en cas de route non trouvé [cas put]
     * @throws Exception en mode debug si la route n'existe pas
     */
    public function putRoute(string $routeName,array $routeParams = []):string{
        return $this->routeFrom(self::$routes["put"],$routeName,$routeParams);
    } 

    /**
     * @param routes liste des routes (tableau du format ([method,name,[params => ...] ])
     * @return string balise javascript contenant la fonction getRouteList contenant les routes nommées
     * @throws Exception en mode debug si la route n'existe pas
     */
    public function jRoute(array $routes):string{
        $jsRoutes = [];

        foreach($routes as $routeData){
            list($method,$name,) = $routeData;

            $routeData[0] = self::$routes[$method];

            $jsRoutes[$name] = $this->routeFrom(...$routeData);
        }

        $jsRoutes = json_encode($jsRoutes);

        return <<<HTML
            <script id="routes-script">
                function getRouteManager(){
                    var routesCopy = JSON.parse('{$jsRoutes}');

                    let route = (route,replaces) => {
                        for(const [toReplace,replace] of Object.entries(replaces) ) route = route.replace(`{\${toReplace}}`,replace)
                        
                        return route;
                    };

                    document.getElementById("routes-script").remove();

                    return {"routes" : routesCopy,"routeReplace" : route};
                }
            </script>
        HTML;   
    }

    /**
     * @param routeName le nom de la route 
     * @param routeParams tableau associatif des paramètres optionneles du lien [nom => valeur]
     * @return string le lien lié à la route ou un lien / en cas de route non trouvé [cas delete]
     * @throws Exception en mode debug si la route n'existe pas
     */
    public function deleteRoute(string $routeName,array $routeParams = []):string{
        return $this->routeFrom(self::$routes["delete"],$routeName,$routeParams);
    } 

    /**
     * @param routes la liste des routes
     * @param routeName le nom de la route 
     * @param routeParams tableau associatif des paramètres optionneles du lien [nom => valeur]
     * @return string le lien lié à la route ou un lien / en cas de route non trouvé
     * @throws Exception en mode debug si la route n'existe pas
     */
    private function routeFrom(array $routes,string $routeName,array $routeParams = []):string{
        foreach($routes as $routeData){
            if($routeData["routeName"] == $routeName){
                $url = $routeData["unmodifiedUrl"];

                // traitement des paramètres
                foreach($routeParams as $param => $replace) $url = str_replace("{{$param}}",$replace,$url);

                return $url;
            }
        }

        if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) ) throw new Exception("La route {$routeName} n'existe pas parmis parmis la méthode sélectionnée !");

        return "/";
    }

    /**
     * récupère les routes du site
     */
    public static function initExtension():void{
        self::$routes = Route::getSiteRoutes();
    }   
}