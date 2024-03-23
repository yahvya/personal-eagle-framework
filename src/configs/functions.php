<?php

/**
 * @brief Fonctions globales utilitaires du framework
 * @attention ces fonctions sont aussi disponible dans blade
 * @attention ne pas modifier le nom des fonctions par défaut ou utiliser le refactor de son ide
 */

use SaboCore\Config\FrameworkConfig;
use SaboCore\Routing\Application\Application;
use SaboCore\Routing\Routes\RouteManager;
use SaboCore\Utils\Csrf\CsrfManager;
use SaboCore\Utils\Session\SessionStorage;
use SaboCore\Utils\String\RandomStringGenerator;
use SaboCore\Utils\String\RandomStringType;

/**
 * @brief Débug les variables fournies
 * @param mixed ...$toDebug variables à débugger
 * @return void
 */
function debug(mixed ...$toDebug):void{
    dump(...$toDebug);
}

/**
 * @brief Débug les variables fournies et quitte le programme
 * @param mixed ...$toDebug variables à débugger
 */
function debugDie(mixed ...$toDebug):never{
    debug(...$toDebug);
    die();
}

/**
 * @brief Recherche une route pour la fournir
 * @param string $requestMethod méthode de requête
 * @param string $routeName nom de la route
 * @param array<string,string> $replaces éléments de remplacements dans le lien
 * @return string|null le lien lié à la route
 */
function route(string $requestMethod,string $routeName,array $replaces = []):string|null{
    $route = RouteManager::findRouteByName(routeName: $routeName,method: $requestMethod);

    if($route === null)
        return null;

    try{
        $link = $route->getRouteLink();

        // recherche et remplacement des paramètres dans le lien
        $variableMatcher = Application::getFrameworkConfig()->getConfig(name: FrameworkConfig::ROUTE_GENERIC_PARAMETER_MATCHER->value);

        foreach($replaces as $variableName => $replace){
            $matcher = preg_replace(pattern: "#\(.*\)#",replacement: $variableName,subject: $variableMatcher);

            $link = preg_replace(pattern: "#$matcher#",replacement: $replace,subject: $link);
        }

        return $link;
    }
    catch (Throwable){}

    return null;
}

/**
 * @brief Génère un token csrf
 * @return CsrfManager le gestion de token
 */
function generateCsrf():CsrfManager{
    $sessionStorage = SessionStorage::create();

    // génération du token
    do
        $token = RandomStringGenerator::generateString(50,false,RandomStringType::SPECIALCHARS);
    while($sessionStorage->getCsrfFrom(token: $token) !== null);

    $manager = new CsrfManager(token: $token);

    $sessionStorage->storeCsrf(csrfManager: $manager);

    return $manager;
}

/**
 * @brief Vérifie que le token soit valide et le supprime si valide
 * @param string $token
 * @return bool si le token est valide
 */
function checkCsrf(string $token):bool{
    $sessionStorage = SessionStorage::create();

    // recherche du token
    $csrfManager = $sessionStorage->getCsrfFrom(token: $token);

    if($csrfManager === null) return false;

    $sessionStorage->deleteCsrf(csrfManager: $csrfManager);

    return true;
}
