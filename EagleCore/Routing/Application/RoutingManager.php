<?php

namespace Yahvya\EagleFramework\Routing\Application;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Yahvya\EagleFramework\Config\ConfigException;
use Yahvya\EagleFramework\Config\EnvConfig;
use Yahvya\EagleFramework\Config\FrameworkConfig;
use Yahvya\EagleFramework\Config\MaintenanceConfig;
use Yahvya\EagleFramework\Controller\Controller;
use Yahvya\EagleFramework\Routing\Request\Request;
use Yahvya\EagleFramework\Routing\Response\HtmlResponse;
use Yahvya\EagleFramework\Routing\Response\RedirectResponse;
use Yahvya\EagleFramework\Routing\Response\Response;
use Yahvya\EagleFramework\Routing\Response\ResourceResponse;
use Yahvya\EagleFramework\Routing\Routes\RouteManager;
use Yahvya\EagleFramework\Utils\Session\FrameworkSession;
use Throwable;

/**
 * @brief Application routing manager
 */
class RoutingManager
{
    /**
     * @var string Provided link
     */
    protected string $link;

    public function __construct()
    {
        $this->link = urldecode(string: parse_url(url: $_SERVER["REQUEST_URI"])["path"] ?? "/");
    }

    /**
     * @brief Launch the application routing process
     * @return Response Http response display handler
     * @throws ConfigException|Throwable On error
     */
    public function start(): Response
    {
        $request = new Request();

        $maintenanceManager = $this->checkMaintenance(request: $request);

        if ($maintenanceManager !== null)
            return $maintenanceManager;

        if ($this->isAccessibleRessource())
            return new ResourceResponse(ressourceAbsolutePath: APP_CONFIG->getConfig(name: "ROOT") . $this->link);

        $searchResult = RouteManager::findRouteByLink(link: $this->link, method: $request->getMethod());

        if ($searchResult == null)
            return self::notFoundPage();

        ["route" => $route, "match" => $match] = $searchResult;
        $matches = $match->matchTable;

        $args = [$request, $matches];

        foreach ($route->accessVerifiers as $verifier)
        {
            $verifyResult = $verifier->execVerification(verifierArgs: $args, onSuccessArgs: $args, onFailureArgs: $args);

            if (!empty($verifyResult["failure"]))
                return $verifyResult["failure"];
        }

        return $this->launch(toExecute: $route->toExecute, matches: $matches, request: $request);
    }

    /**
     * @return bool Check if the link is an authorized resource link
     * @throws ConfigException On error
     */
    protected function isAccessibleRessource(): bool
    {
        $frameworkConfig = Application::getFrameworkConfig();

        return
            // Check if the path is in the public directory or contain an authorized extension
            (
                str_starts_with(haystack: $this->link, needle: $frameworkConfig->getConfig(name: FrameworkConfig::PUBLIC_DIR_PATH->value)) ||
                !empty(
                array_filter(
                    array: $frameworkConfig->getConfig(FrameworkConfig::AUTHORIZED_EXTENSIONS_AS_PUBLIC->value),
                    callback: fn(string $extension): bool => str_ends_with($this->link, $extension)
                )
                )
            ) &&
            @file_exists(filename: APP_CONFIG->getConfig(name: "ROOT") . $this->link);
    }

    /**
     * @brief Launch the treatment method
     * @param array|Closure $toExecute Execution handler
     * @param array $matches Url matches
     * @param Request $request Request data handler
     * @return Response Provided response
     * @throws Throwable On error
     */
    protected function launch(array|Closure $toExecute, array $matches, Request $request): Response
    {
        if ($toExecute instanceof Closure)
        {
            $callable = $toExecute;
            $reflectionMethod = new ReflectionFunction(function: $toExecute);
        }
        else if (is_subclass_of(object_or_class: $toExecute[0], class: Controller::class))
        {
            $instance = new ReflectionClass(objectOrClass: $toExecute[0])->newInstance();
            $callable = [$instance, $toExecute[1]];
            $reflectionMethod = new ReflectionMethod(objectOrMethod: $instance, method: $toExecute[1]);
        }
        else throw new ConfigException(message: "Unknown callable");

        $args = [];

        // Assign expected parameters
        foreach ($reflectionMethod->getParameters() as $parameter)
        {
            $type = $parameter->getType();

            if ($type !== null && $type->getName() === Request::class)
            {
                $args[] = $request;
                continue;
            }

            $parameterName = $parameter->getName();

            if (array_key_exists(key: $parameterName, array: $matches))
                $args[] = $matches[$parameterName];
        }

        // Flash data handling @flash_data
        $request->sessionStorage->manageFlashDatas();

        return call_user_func_array(callback: $callable, args: $args);
    }

    /**
     * @brief Check maintenance mode
     * @param Request $request Request
     * @return Response|null Maintenance response or null if the access is authorized
     * @throws ConfigException|Throwable On error
     */
    protected function checkMaintenance(Request $request): Response|null
    {
        $maintenanceConfig = Application::getEnvConfig()->getConfig(name: EnvConfig::MAINTENANCE_CONFIG->value);
        $maintenanceSecretLink = $maintenanceConfig->getConfig(name: MaintenanceConfig::SECRET_LINK->value);

        if (
            !$maintenanceConfig->getConfig(name: MaintenanceConfig::IS_IN_MAINTENANCE->value) ||
            $this->canAccessOnMaintenance(request: $request)
        ) return null;

        if ($this->link !== $maintenanceSecretLink)
            return self::maintenancePage();

        $maintenanceManager = new ReflectionClass(
            objectOrClass: $maintenanceConfig->getConfig(name: MaintenanceConfig::ACCESS_MANAGER->value)
        )->newInstance();

        if ($_SERVER["REQUEST_METHOD"] === "POST")
        {
            if ($maintenanceManager->verifyLogin(request: $request))
            {
                $this->authorizeAccessOnMaintenance(request: $request);
                return new RedirectResponse(link: "/");
            }
            else return new RedirectResponse(link: $maintenanceSecretLink);
        }
        else return $maintenanceManager->showMaintenancePage(secretLink: $maintenanceSecretLink);
    }

    /**
     * @param Request $request Request data handler
     * @return bool If the access is authorized during the maintenance
     */
    protected function canAccessOnMaintenance(Request $request): bool
    {
        return $request->sessionStorage->getFrameworkValue(storeKey: FrameworkSession::MAINTENANCE_ACCESS->value) !== null;
    }

    /**
     * @param Request $request Request data handler
     * @brief Authorize the access during maintenance
     * @return void
     */
    protected function authorizeAccessOnMaintenance(Request $request): void
    {
        $request->sessionStorage->storeFramework(storeKey: FrameworkSession::MAINTENANCE_ACCESS->value, toStore: true);
    }

    /**
     * @return HtmlResponse Page not found
     * @throws ConfigException On error
     */
    public static function notFoundPage(): HtmlResponse
    {
        return new HtmlResponse(
            content: @file_get_contents(APP_CONFIG->getConfig(name: "ROOT") . "/Src/views/default-pages/not-found.html") ??
            "Page non trouvé"
        );
    }

    /**
     * @return HtmlResponse Maintenance page response
     * @throws ConfigException On error
     */
    public static function maintenancePage(): HtmlResponse
    {
        return new HtmlResponse(
            content: @file_get_contents(APP_CONFIG->getConfig(name: "ROOT") . "/Src/views/default-pages/maintenance.html") ??
            "Site en cours de maintenance"
        );
    }
}
