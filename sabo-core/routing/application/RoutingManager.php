<?php

namespace SaboCore\Routing\Application;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use SaboCore\Config\ConfigException;
use SaboCore\Config\EnvConfig;
use SaboCore\Config\FrameworkConfig;
use SaboCore\Config\MaintenanceConfig;
use SaboCore\Controller\Controller;
use SaboCore\Routing\Request\Request;
use SaboCore\Routing\Response\HtmlResponse;
use SaboCore\Routing\Response\RedirectResponse;
use SaboCore\Routing\Response\Response;
use SaboCore\Routing\Response\ResourceResponse;
use SaboCore\Routing\Routes\RouteManager;
use SaboCore\Utils\Session\FrameworkSession;
use Throwable;

/**
 * @brief Gestionnaire du routing de l'application
 */
class RoutingManager{
    /**
     * @var string lien fourni
     */
    protected string $link;

    public function __construct(){
        $this->link = urldecode(string: parse_url($_SERVER["REQUEST_URI"])["path"] ?? "/");
    }

    /**
     * @brief Lance le routing de l'application
     * @return Response la réponse à afficher
     * @throws ConfigException|Throwable en cas d'erreur
     */
    public function start():Response{
        $request = new Request();

        // vérification de maintenance
        $maintenanceManager = $this->checkMaintenance(request: $request);

        if($maintenanceManager !== null) return $maintenanceManager;

        // vérification d'accès à une ressource
        if($this->isAccessibleRessource() )
            return new ResourceResponse(ressourceAbsolutePath: APP_CONFIG->getConfig(name: "ROOT") . $this->link);

        // recherche de l'action à faire
        $searchResult = RouteManager::findRouteByLink(link: $this->link,method: $request->getMethod());

        // affichage de la page non trouvée
        if($searchResult == null)
            return self::notFoundPage();

        // vérification des conditions d'accès
        ["route" => $route,"match" => $match] = $searchResult;
        $matches = $match->getMatchTable();

        $args = [$request,$matches];

        // récupération et vérification des conditions
        foreach($route->getAccessVerifiers() as $verifier) {
            $verifyResult = $verifier->execVerification(verifierArgs: $args,onSuccessArgs: $args,onFailureArgs: $args);

            if(!empty($verifyResult["failure"]) )
                return $verifyResult["failure"];
        }

        // lancement du programme
        return $this->launch(toExecute: $route->getToExecute(),matches: $matches,request: $request);
    }

    /**
     * @brief Vérifie si le lien est celui d'une ressource autorisée à l'accès par lien
     * @return bool si le lien est celui d'une ressource autorisée à l'accès par lien
     * @throws ConfigException
     */
    protected function isAccessibleRessource():bool{
        $frameworkConfig = Application::getFrameworkConfig();

        return
            // on vérifie si le chemin se trouve dans le dossier public, ou est une extension autorisée
            (
                str_starts_with(haystack: $this->link,needle: $frameworkConfig->getConfig(name: FrameworkConfig::PUBLIC_DIR_PATH->value)) ||
                !empty(
                    array_filter(
                        array: $frameworkConfig->getConfig(FrameworkConfig::AUTHORIZED_EXTENSIONS_AS_PUBLIC->value),
                        callback: fn(string $extension):bool => str_ends_with($this->link,$extension)
                    )
                )
            ) &&
            // on vérifie que le fichier existe
            @file_exists(filename: APP_CONFIG->getConfig(name: "ROOT") . $this->link);
    }

    /**
     * @brief lance la fonction de traitement
     * @param array|Closure $toExecute l'action à exécuter
     * @param array $matches les matchs dans l'URL
     * @param Request $request la requête
     * @return Response la réponse fournie
     * @throws Throwable en cas d'erreur
     */
    protected function launch(array|Closure $toExecute,array $matches,Request $request):Response{
        if($toExecute instanceof Closure){
            $callable = $toExecute;
            $reflectionMethod = new ReflectionFunction(function: $toExecute);
        }
        elseif(is_subclass_of(object_or_class: $toExecute[0],class: Controller::class) ){
            $instance = (new ReflectionClass(objectOrClass: $toExecute[0]))->newInstance();
            $callable = [$instance,$toExecute[1]];
            $reflectionMethod = new ReflectionMethod(objectOrMethod: $instance,method: $toExecute[1]);
        }
        else throw new ConfigException(message: "Callable inconnu");

        $args = [];

        // affectation des paramètres attendue
        foreach($reflectionMethod->getParameters() as $parameter){
            // recherche de l'argument request
            $type = $parameter->getType();

            if($type !== null && $type->getName() === Request::class){
                $args[] = $request;
                continue;
            }

            // recherche de l'argument paramètre de l'URL
            $parameterName = $parameter->getName();

            if(array_key_exists(key: $parameterName,array: $matches) )
                $args[] = $matches[$parameterName];
        }

        // gestion des données flash
        $request->getSessionStorage()->manageFlashDatas();

        return call_user_func_array(callback: $callable,args: $args);
    }

    /**
     * @brief Vérifie la gestion de la maintenance
     * @param Request $request requête
     * @return Response|null la réponse ou null si accès autorisé
     * @throws ConfigException|Throwable en cas d'erreur
     */
    protected function checkMaintenance(Request $request):Response|null{
        $maintenanceConfig = Application::getEnvConfig()->getConfig(name: EnvConfig::MAINTENANCE_CONFIG->value);
        $maintenanceSecretLink = $maintenanceConfig->getConfig(name: MaintenanceConfig::SECRET_LINK->value);

        if(
            !$maintenanceConfig->getConfig(name: MaintenanceConfig::IS_IN_MAINTENANCE->value) ||
            $this->canAccessOnMaintenance(request: $request)
        ) return null;

        if($this->link !== $maintenanceSecretLink)
            return self::maintenancePage();

        $maintenanceManager = (new ReflectionClass($maintenanceConfig->getConfig(name: MaintenanceConfig::ACCESS_MANAGER->value)))->newInstance();

        // si la requête est POST authentification sinon affichage de la page d'authentification
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            if($maintenanceManager->verifyLogin(request: $request) ){
                $this->authorizeAccessOnMaintenance(request: $request);
                return new RedirectResponse(link: "/");
            }
            else return new RedirectResponse(link: $maintenanceSecretLink);
        }
        else return $maintenanceManager->showMaintenancePage(secretLink: $maintenanceSecretLink);
    }

    /**
     * @param Request $request gestionnaire de requête
     * @return bool si l'utilisateur a accès au site
     */
    protected function canAccessOnMaintenance(Request $request):bool{
        return $request->getSessionStorage()->getFrameworkValue(storeKey: FrameworkSession::MAINTENANCE_ACCESS->value) !== null;
    }

    /**
     * @param Request $request gestionnaire de requête
     * @brief Autorise l'accès durant la maintenance
     * @return void
     */
    protected function authorizeAccessOnMaintenance(Request $request):void{
        $request->getSessionStorage()->storeFramework(storeKey: FrameworkSession::MAINTENANCE_ACCESS->value,toStore: true);
    }

    /**
     * @return HtmlResponse la page non trouvée
     * @throws ConfigException en cas d'erreur de configuration
     */
    public static function notFoundPage():HtmlResponse{
        return new HtmlResponse(
            content: @file_get_contents(APP_CONFIG->getConfig(name: "ROOT") . "/src/views/default-pages/not-found.html") ??
            "Page non trouvé"
        );
    }

    /**
     * @return HtmlResponse la page de maintenance
     * @throws ConfigException en cas d'erreur de configuration
     */
    public static function maintenancePage():HtmlResponse{
        return new HtmlResponse(
            content: @file_get_contents(APP_CONFIG->getConfig(name: "ROOT") . "/src/views/default-pages/maintenance.html") ??
            "Site en cours de maintenance"
        );
    }
}
