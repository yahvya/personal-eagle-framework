<?php

namespace SaboCore\Application\Application;

use Closure;

/**
 * @brief application cycle manager
 * @attention based on the linked step in the life cycle, certain functions / functionalities could not be available
 * @method static void onErrorInCycle(Closure|Callable $action) action at step ERROR_IN_CYCLE
 * @method static void onInit(Closure|Callable $action) action at step INIT
 * @method static void onConfigLoaded(Closure|Callable $action) action at step CONFIG_LOADED
 * @method static void beforeDatabaseInit(Closure|Callable $action) action at step BEFORE_DATABASE_INIT
 * @method static void afterDatabaseInit(Closure|Callable $action) action at step AFTER_DATABASE_INIT
 * @method static void onStartRouting(Closure|Callable $action) action at step START_ROUTING
 * @method static void onCheckMaintenance(Closure|Callable $action) action at step CHECK_MAINTENANCE
 * @method static void onMaintenanceBlock(Closure|Callable $action) action at step MAINTENANCE_BLOCK
 * @method static void onRouteFounded(Closure|Callable $action) action at step ROUTE_FOUNDED
 * @method static void onRouteVerifierFail(Closure|Callable $action) action at step ROUTE_VERIFIER_FAILED
 * @method static void onRouteNotFounded(Closure|Callable $action) action at step ROUTE_NOT_FOUND
 * @method static void onRenderResponse(Closure|Callable $action) action at step RENDER_RESPONSE
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
        "onMaintenanceBlock" => ApplicationCycle::MAINTENANCE_BLOCK,
        "onRouteFounded" => ApplicationCycle::ROUTE_FOUNDED,
        "onRouteVerifierFail" => ApplicationCycle::ROUTE_VERIFIER_FAILED,
        "onRouteNotFounded" => ApplicationCycle::ROUTE_NOT_FOUND,
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