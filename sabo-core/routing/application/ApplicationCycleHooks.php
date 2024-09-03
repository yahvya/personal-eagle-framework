<?php

namespace SaboCore\Routing\Application;

use Closure;

/**
 * @brief Gestionnaire des évènements du cycle de l'application
 * @attention en fonction de l'étape il se peut que certaines parties et / ou fonctions de l'application ne soient pas chargées
 * @method static void onErrorInCycle(Closure $action) action à l'évènement ERROR_IN_CYCLE
 * @method static void onInit(Closure $action) action à l'évènement INIT
 * @method static void onConfigLoaded(Closure $action) action à l'évènement CONFIG_LOADED
 * @method static void beforeDatabaseInit(Closure $action) action à l'évènement BEFORE_DATABASE_INIT
 * @method static void afterDatabaseInit(Closure $action) action à l'évènement AFTER_DATABASE_INIT
 * @method static void onStartRouting(Closure $action) action à l'évènement START_ROUTING
 * @method static void onCheckMaintenance(Closure $action) action à l'évènement CHECK_MAINTENANCE
 * @method static void onCheckResourceRequired(Closure $action) action à l'évènement CHECK_RESOURCE_REQUIRED
 * @method static void onRouteFounded(Closure $action) action à l'évènement ROUTE_FOUNDED
 * @method static void onRouteVerifierFail(Closure $action) action à l'évènement ROUTE_VERIFIER_FAILED
 * @method static void onRenderResponse(Closure $action) action à l'évènement RENDER_RESPONSE
 */
abstract class ApplicationCycleHooks{
    /**
     * @var Array<ApplicationCycle,Closure>
     */
    public static array $hooksManagers = [];

    /**
     * @var array indicé par le nom de la méthode et l'évènement lié
     */
    protected static array $authorizedMethods = [
        "onErrorInCycle" => ApplicationCycle::ERROR_IN_CYCLE,
        "onInit" => ApplicationCycle::INIT,
        "onConfigLoaded" => ApplicationCycle::CONFIG_LOADED,
        "beforeDatabaseInit" => ApplicationCycle::BEFORE_DATABASE_INIT,
        "afterDatabaseInit" => ApplicationCycle::AFTER_DATABASE_INIT,
        "onStartRouting" => ApplicationCycle::START_ROUTING,
        "onCheckMaintenance" => ApplicationCycle::CHECK_MAINTENANCE,
        "onCheckResourceRequired" => ApplicationCycle::CHECK_RESOURCE_REQUIRED,
        "onRouteFounded" => ApplicationCycle::ROUTE_FOUNDED,
        "onRouteVerifierFail" => ApplicationCycle::ROUTE_VERIFIER_FAILED,
        "onRenderResponse" => ApplicationCycle::RENDER_RESPONSE,
    ];

    public static function __callStatic($name, $arguments):void{
        if(
            !array_key_exists(key: $name,array: self::$authorizedMethods) ||
            empty($arguments) ||
            !(($arguments = array_values(array: $arguments))[0] instanceof Closure)
        )
            return;

        $cycleStep = self::$authorizedMethods[$name];

        self::$hooksManagers[$cycleStep->value] = $arguments[0];
    }

    /**
     * @brief Lance la fonction d'exécution liée à l'étape fournie si définie
     * @param ApplicationCycle $cycleStep étape
     * @param mixed ...$args paramètres de la fonction liée
     * @return mixed le retour de la fonction ou true par défaut
     */
    public static function call(ApplicationCycle $cycleStep,mixed ...$args):mixed{
        return array_key_exists(key: $cycleStep->value,array: self::$hooksManagers) ?
            call_user_func_array(callback: self::$hooksManagers[$cycleStep->value],args: $args) :
            true;
    }
}