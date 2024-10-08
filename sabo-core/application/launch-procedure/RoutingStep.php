<?php

namespace SaboCore\Application\ApplicationLaunchProcedure;

use Closure;
use Exception;
use Override;
use PhpAddons\ProcedureManager\Procedure;
use PhpAddons\ProcedureManager\ProcedureStep;
use SaboCore\Application\Application\ApplicationCycle;
use SaboCore\Application\Application\ApplicationCycleHooks;
use SaboCore\Application\Application\ApplicationState;
use SaboCore\Configuration\MaintenanceConfiguration;
use SaboCore\Routing\Request\FrameworkStorageMapping;
use SaboCore\Routing\Request\Request;
use SaboCore\Routing\Routes\Route;
use SaboCore\Utils\Injection\injector\DependencyInjector;

/**
 * @brief routing step
 */
class RoutingStep implements ProcedureStep{
    public function __construct(){
        ApplicationState::$request = new Request();
    }

    /**
     * @param Procedure $procedure parent procedure
     * @param mixed ...$args arguments
     * @return bool can access next
     * @throws Exception on error
     */
    #[Override]
    public function canAccessNext(Procedure $procedure, mixed ...$args): bool{
        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::START_ROUTING);

        if(!$this->checkMaintenance())
            return true;

        $this->searchRoute();

        return true;
    }

    /**
     * @brief check maintenance state
     * @return bool can access
     */
    protected function checkMaintenance():bool{
        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::CHECK_MAINTENANCE);

        # check maintenance state

        $request = ApplicationState::$request;

        if(!maintenanceEnv(key: MaintenanceConfiguration::IS_IN_MAINTENANCE)){
            $request
                ->sessionManager
                ->deleteInFramework(storeKey: FrameworkStorageMapping::MAINTENANCE_CONFIG);
            return true;
        }

        $sessionMaintenance = $request
            ->sessionManager
            ->getFrameworkValue(storeKey: FrameworkStorageMapping::MAINTENANCE_CONFIG);

        # check if the access is already acquired
        if($sessionMaintenance !== null && array_key_exists(key: "unlockedAccessAt",array: $sessionMaintenance))
            return true;

        # check the count of try

        if($sessionMaintenance === null)
            $sessionMaintenance = ["countOfTry" => 0];

        if($sessionMaintenance["countOfTry"] + 1 > maintenanceEnv(key: MaintenanceConfiguration::MAX_TRY)){
            ApplicationCycleHooks::call(cycleStep: ApplicationCycle::MAINTENANCE_BLOCK);
            return false;
        }

        $request
            ->sessionManager
            ->storeFramework(storeKey: FrameworkStorageMapping::MAINTENANCE_CONFIG,toStore: ["countOfTry" => $sessionMaintenance["countOfTry"] + 1]);

        # try to acquire access (check method, check link , check code presence, compare code, unlock access)

        if($request->requestMethod !== "get"){
            ApplicationCycleHooks::call(cycleStep: ApplicationCycle::MAINTENANCE_BLOCK);
            return false;
        }

        if(
            $request
                ->uriMatcher
                ->matchPattern(pattern: maintenanceEnv(key: MaintenanceConfiguration::SECRET_ACCESS_LINK)) === null
        ){
            ApplicationCycleHooks::call(cycleStep: ApplicationCycle::MAINTENANCE_BLOCK);
            return false;
        }

        $validationCode = $request
            ->getValues
            ->get(key: maintenanceEnv(key: MaintenanceConfiguration::GET_VARIABLE_NAME));

        if(
            $validationCode === null ||
            !password_verify(password: $validationCode,hash: maintenanceEnv(key: MaintenanceConfiguration::ACCESS_CODE))
        ){
            ApplicationCycleHooks::call(cycleStep: ApplicationCycle::MAINTENANCE_BLOCK);
            return false;
        }

        $request
            ->sessionManager
            ->storeFramework(storeKey: FrameworkStorageMapping::MAINTENANCE_CONFIG,toStore: [
                "unlockedAccessAt" => time(),
                "countOfTry" => $sessionMaintenance["countOfTry"] + 1
            ]);

        return true;
    }

    /**
     * @brief search the matched route
     * @return $this
     * @throws Exception on error
     */
    protected function searchRoute():static{
        $request = ApplicationState::$request;
        $requestMethod = $request->requestMethod;
        $genericParamsMatchRegex = Route::getGenericParamsMatchRegex();

        foreach(Route::$routes[$requestMethod] as $route){
            # check if it's the searched route
            $matches = $request
                ->uriMatcher
                ->matchPattern(
                    pattern: $route->link,
                    genericParamsMatcherRegex: $genericParamsMatchRegex,
                    genericParamsCustomRegex: $route->genericParamsCustomRegex
                );

            if($matches === null)
                continue;

            ApplicationCycleHooks::call(ApplicationCycle::ROUTE_FOUNDED,[
                "route" => $route,
                "match" => $matches
            ]);

            # check access verifiers
            $break = false;

            foreach($route->accessConditions as $conditionCallable){
                if(!call_user_func(callback: $conditionCallable)){
                    ApplicationCycleHooks::call(ApplicationCycle::ROUTE_VERIFIER_FAILED,$conditionCallable);
                    $break = true;
                    break;
                }
            }

            if($break)
                break;

            # call the executor
            $arguments = DependencyInjector::buildCallableArgs(callable: $route->executor,baseElements: $matches);

            $callable = $route->executor instanceof Closure ?
                $route->executor :
                [ApplicationState::$injector->createFromClass(class: $route->executor[0]),$route->executor[1]];

            call_user_func_array(callback: $callable,args: $arguments)->render();

            return $this;
        }

        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::ROUTE_NOT_FOUND);
        return $this;
    }
}