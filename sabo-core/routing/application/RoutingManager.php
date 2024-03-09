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
use SaboCore\Routing\Response\RessourceResponse;
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
        $this->link = parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
    }

    /**
     * @brief Lance le routing de l'application
     * @return Response la réponse à afficher
     * @throws ConfigException|Throwable en cas d'erreur
     */
    public function start():Response{
        // chargement des routes
        require_once(APP_CONFIG->getConfig("ROOT") . Application::getFrameworkConfig()->getConfig(FrameworkConfig::ROUTES_BASEDIR_PATH->value) . "/routes.php");

        $request = new Request();

        // vérification de maintenance
        $maintenanceManager = $this->checkMaintenance($request);

        if($maintenanceManager !== null) return $maintenanceManager;

        // vérification d'accès à une ressource
        if($this->isAccessibleRessource() ) return new RessourceResponse(APP_CONFIG->getConfig("ROOT") . $this->link);

        // recherche de l'action à faire
        $searchResult = RouteManager::findRouteByLink($this->link);

        // affichage de la page non trouvée
        if($searchResult == null) return self::notFoundPage();

        // vérification des conditions d'accès
        ["route" => $route,"match" => $match] = $searchResult;
        $matches = $match->getMatchTable();

        $args = [$request,$matches];

        // récupération et vérification des conditions
        foreach($route->getAccessVerifiers() as $verifier) {
            $verifyResult = $verifier->execVerification($args,$args,$args);

            if(!empty($verifyResult["failure"]) ) return $verifyResult["failure"];
        }

        // lancement du programme
        return $this->launch($route->getToExecute(),$matches,$request);
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
                str_starts_with($this->link,$frameworkConfig->getConfig(FrameworkConfig::PUBLIC_DIR_PATH->value)) ||
                !empty(
                    array_filter(
                        $frameworkConfig->getConfig(FrameworkConfig::AUTHORIZED_EXTENSIONS_AS_PUBLIC->value),
                        fn(string $extension):bool => str_ends_with($this->link,$extension)
                    )
                )
            ) &&
            // on vérifie que le fichier existe
            file_exists(APP_CONFIG->getConfig("ROOT") . $this->link);
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
            $reflectionMethod = new ReflectionFunction($toExecute);
        }
        elseif(is_subclass_of($toExecute[0],Controller::class) ){
            $instance = (new ReflectionClass($toExecute[0]))->newInstance();
            $callable = [$instance,$toExecute[1]];
            $reflectionMethod = new ReflectionMethod($instance,$toExecute[1]);
        }
        else throw new ConfigException("Callable inconnu");

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

            if(array_key_exists($parameterName,$matches) )
                $args[] = $matches[$parameterName];
        }

        // gestion des données flash
        $request->getSessionStorage()->manageFlashDatas();

        return call_user_func_array($callable,$args);
    }

    /**
     * @brief Vérifie la gestion de la maintenance
     * @param Request $request requête
     * @return Response|null la réponse ou null si accès autorisé
     * @throws ConfigException|Throwable en cas d'erreur
     */
    protected function checkMaintenance(Request $request):Response|null{
        $maintenanceConfig = Application::getEnvConfig()->getConfig(EnvConfig::MAINTENANCE_CONFIG->value);
        $maintenanceSecretLink = $maintenanceConfig->getConfig(MaintenanceConfig::SECRET_LINK->value);

        if(!$maintenanceConfig->getConfig(MaintenanceConfig::IS_IN_MAINTENANCE->value) || $this->canAccessOnMaintenance($request) ) return null;
        if($this->link !== $maintenanceSecretLink) return self::maintenancePage();

        $maintenanceManager = (new ReflectionClass($maintenanceConfig->getConfig(MaintenanceConfig::ACCESS_MANAGER->value)))->newInstance();

        // si la requête est POST authentification sinon affichage de la page d'authentification
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            if($maintenanceManager->verifyLogin() ){
                $this->authorizeAccessOnMaintenance($request);
                return new RedirectResponse("/");
            }
            else return new RedirectResponse($maintenanceSecretLink);
        }
        else return $maintenanceManager->showMaintenancePage($maintenanceSecretLink);
    }

    /**
     * @param Request $request gestionnaire de requête
     * @return bool si l'utilisateur a accès au site
     */
    protected function canAccessOnMaintenance(Request $request):bool{
        return $request->getSessionStorage()->getFrameworkValue(FrameworkSession::MAINTENANCE_ACCESS->value) !== null;
    }

    /**
     * @param Request $request gestionnaire de requête
     * @brief Autorise l'accès durant la maintenance
     * @return void
     */
    protected function authorizeAccessOnMaintenance(Request $request):void{
        $request->getSessionStorage()->storeFramework(FrameworkSession::MAINTENANCE_ACCESS->value,true);
    }

    /**
     * @return HtmlResponse la page non trouvée
     * @throws ConfigException en cas d'erreur de configuration
     */
    public static function notFoundPage():HtmlResponse{
        return new HtmlResponse(
            @file_get_contents(APP_CONFIG->getConfig("ROOT") . "/src/views/default-pages/not-found.html") ??
            "Page non trouvé"
        );
    }

    /**
     * @return HtmlResponse la page de maintenance
     * @throws ConfigException en cas d'erreur de configuration
     */
    public static function maintenancePage():HtmlResponse{
        return new HtmlResponse(
            @file_get_contents(APP_CONFIG->getConfig("ROOT") . "/src/views/default-pages/maintenance.html") ??
            "Site en cours de maintenance"
        );
    }


}