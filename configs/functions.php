<?php

/**
 * @brief Application global functions
 * @attention These methods are available in 'blade' too
 * @attention Do not edit the default functions name without your ide refactor functionality, they are probably used elsewhere
 */

use Yahvya\EagleFramework\Config\FrameworkConfig;
use Yahvya\EagleFramework\Routing\Application\Application;
use Yahvya\EagleFramework\Routing\Routes\RouteManager;
use Yahvya\EagleFramework\Utils\Csrf\CsrfManager;
use Yahvya\EagleFramework\Utils\Session\SessionStorage;
use Yahvya\EagleFramework\Utils\String\RandomStringGenerator;
use Yahvya\EagleFramework\Utils\String\RandomStringType;

/**
 * @brief Use the symfony dump method to debug variables, and the program will continue
 * @param mixed ...$toDebug To debug
 * @return void
 */
function debug(mixed ...$toDebug): void
{
    dump(...$toDebug);
}

/**
 * @brief Use the symfony dump method to debug variables, and the program stops there
 * @param mixed ...$toDebug To debug
 */
function debugDie(mixed ...$toDebug): never
{
    debug(...$toDebug);
    die();
}

/**
 * @brief Find a route
 * @param string $requestMethod Associated request method
 * @param string $routeName Route name
 * @param array{string:string} $replaces Generic parameters replacements
 * @return string|null Founded link transformed or null
 */
function route(string $requestMethod, string $routeName, array $replaces = []): string|null
{
    $route = RouteManager::findRouteByName(routeName: $routeName, method: $requestMethod);

    if ($route === null)
        return null;

    try
    {
        $link = $route->link;

        $variableMatcher = Application::getFrameworkConfig()->getConfig(name: FrameworkConfig::ROUTE_GENERIC_PARAMETER_MATCHER->value);

        foreach ($replaces as $variableName => $replace)
        {
            $matcher = preg_replace(pattern: "#\(.*\)#", replacement: $variableName, subject: $variableMatcher);
            $link = preg_replace(pattern: "#$matcher#", replacement: $replace, subject: $link);
        }

        return $link;
    }
    catch (Throwable)
    {
        return null;
    }
}

/**
 * @brief Generate a csrf token
 * @return CsrfManager Token manager
 */
function generateCsrf(): CsrfManager
{
    $sessionStorage = SessionStorage::create();

    do
        $token = RandomStringGenerator::generateString(50, false, RandomStringType::SPECIAL_CHARS);
    while ($sessionStorage->getCsrfFrom(token: $token) !== null);

    $manager = new CsrfManager(token: $token);

    $sessionStorage->storeCsrf(csrfManager: $manager);

    return $manager;
}

/**
 * @brief Check if the provided token value is a valid csrf token
 * @param string $token Token value
 * @return bool If valid
 */
function checkCsrf(string $token): bool
{
    $sessionStorage = SessionStorage::create();

    $csrfManager = $sessionStorage->getCsrfFrom(token: $token);

    if ($csrfManager === null) return false;

    $sessionStorage->deleteCsrf(csrfManager: $csrfManager);

    return true;
}
