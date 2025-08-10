<?php

use Yahvya\EagleFramework\Routing\Routes\RouteManager;

/**
 * @brief Blade configuration file
 */

// Create your blade functions there. It is recommended to add the 'blade' prefix to each function defined there

/**
 * @brief Provided route access to the website JavaScript part
 * @param array<array{string:mixed}> $routes Route List. Each array should have the format [method => "", name => "", [params_replacements → ...]])
 * @param string|null $funcNameReplace The callable function in JavaScript will be called (getRouteManager), this parameter allows replacing this name
 * @param string|null $customIdReplace The generated script tag have the id "routes-script", this parameter allows replacing this id
 * @return string JavaScript generated tag
 */
function bladeJsRoutes(array $routes, ?string $funcNameReplace = null, ?string $customIdReplace = null): string
{
    $jsRoutes = [];

    foreach ($routes as $routeData)
    {
        list($method, $name,) = $routeData;

        $route = RouteManager::findRouteByName(routeName: $name, method: $method);

        if ($route === null) continue;

        $jsRoutes[$name] = $route->link;
    }

    $jsRoutes = @json_encode(value: $jsRoutes);

    $name = $funcNameReplace ?? "getRouteManager";
    $scriptId = $customIdReplace ?? "routes-script";

    return <<<HTML
        <script id="{$scriptId}">
            function {$name}(){
                const routesCopy = JSON.parse('{$jsRoutes}')

                let route = (route,replaces) => {
                    for(const [toReplace,replace] of Object.entries(replaces) ) route = route.replace(`{\${toReplace}}`,replace)
                    
                    return route
                }

                document.getElementById("{$scriptId}").remove()

                return {"routes" : routesCopy,"routeReplace" : route}
            }
        </script>
    HTML;
}

/**
 * @return array{string:Closure} Blade registered directives
 */
function registerBladeDirectives(): array
{
    return [

    ];
}