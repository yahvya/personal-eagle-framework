<?php

use SaboCore\Routing\Routes\RouteManager;

/**
 * @brief Fichier de configuration blade
 */

// Créez ici les fonctions. Il est recommandé de préfixer les fonctions par blade (ex : function bladeCustomFunction(){})

/**
 * @brief Fourni les routes à javascript
 * @param array $routes liste des routes (tableau du format ([method, name, [params → ...]])
 * @param string|null $funcNameReplace nom de remplacement pour la fonction js
 * @param string|null $customIdReplace  nom de remplacement pour l'id du script js
 * @return string balise javascript contenant la fonction getRouteList contenant les routes nommées
 */
function bladeJsRoutes(array $routes,?string $funcNameReplace = null,?string $customIdReplace = null):string{
    $jsRoutes = [];

    foreach($routes as $routeData){
        list($method,$name,) = $routeData;

        $route = RouteManager::findRouteByName(routeName: $name,method: $method);

        if($route === null) continue;

        $jsRoutes[$name] = $route->getRouteLink();
    }

    $jsRoutes = @json_encode(value: $jsRoutes);

    $name = $funcNameReplace ?? "getRouteManager";
    $scriptId = $customIdReplace ?? "routes-script";

    return <<<HTML
            <script id="{$scriptId}">
                function {$name}(){
                    var routesCopy = JSON.parse('{$jsRoutes}');

                    let route = (route,replaces) => {
                        for(const [toReplace,replace] of Object.entries(replaces) ) route = route.replace(`{\${toReplace}}`,replace)
                        
                        return route;
                    };

                    document.getElementById("{$scriptId}").remove();

                    return {"routes" : routesCopy,"routeReplace" : route};
                }
            </script>
        HTML;
}

// Remplissez la tableau de vos directives blades

/**
 * @return array<string,Closure> tableau associatif avec en clé le nom de la directive blade et en valeur la fonction de traitement
 */
function registerBladeDirectives():array{
    return [

    ];
}