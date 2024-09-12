<?php

namespace SaboCore\Application\ApplicationLaunchProcedure;

use PhpAddons\ProcedureManager\Procedure;
use PhpAddons\ProcedureManager\ProcedureStep;
use SaboCore\Application\Application\ApplicationCycle;
use SaboCore\Application\Application\ApplicationCycleHooks;
use SaboCore\Application\Application\ApplicationState;
use SaboCore\Configuration\MaintenanceConfiguration;
use SaboCore\Routing\Request\FrameworkStorageMapping;
use SaboCore\Routing\Request\Request;
use SaboCore\Routing\Routes\Route;

/**
 * @brief routing step
 */
class RoutingStep implements ProcedureStep{
    public function __construct(){
        ApplicationState::$request = new Request();
    }

    public function canAccessNext(Procedure $procedure, ...$args): bool{
        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::START_ROUTING);

        $this
            ->checkMaintenance()
            ->searchRoute();

        return true;
    }

    /**
     * @brief check maintenance state
     * @return $this
     */
    protected function checkMaintenance():static{
        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::CHECK_MAINTENANCE);

        # check maintenance state

        if(!maintenanceEnv(key: MaintenanceConfiguration::IS_IN_MAINTENANCE))
            return $this;

        $request = ApplicationState::$request;

        # check if the access is already acquired

        if(
            $request
                ->sessionManager
                ->getFrameworkValue(storeKey: FrameworkStorageMapping::MAINTENANCE_CONFIG) !== null
        )
            return $this;

        # try to acquire access (check method, check link , check code presence, compare code, unlock access)

        if($request->requestMethod !== "get"){
            ApplicationCycleHooks::call(cycleStep: ApplicationCycle::MAINTENANCE_BLOCK);
            return $this;
        }

        if(
            $request
                ->uriMatcher
                ->matchPattern(pattern: maintenanceEnv(key: MaintenanceConfiguration::SECRET_ACCESS_LINK)) === null
        ){
            ApplicationCycleHooks::call(cycleStep: ApplicationCycle::MAINTENANCE_BLOCK);
            return $this;
        }

        $validationCode = $request
            ->getValues
            ->get(key: maintenanceEnv(key: MaintenanceConfiguration::GET_VARIABLE_NAME));

        if(
            $validationCode === null ||
            !password_verify(password: $validationCode,hash: maintenanceEnv(key: MaintenanceConfiguration::ACCESS_CODE))
        ){
            ApplicationCycleHooks::call(cycleStep: ApplicationCycle::MAINTENANCE_BLOCK);
            return $this;
        }

        $request
            ->sessionManager
            ->storeFramework(storeKey: FrameworkStorageMapping::MAINTENANCE_CONFIG,toStore: ["unlockedAccessAt" => time()]);

        return $this;
    }

    /**
     * @brief search the matched route
     * @return $this
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
                    genericParamsMatcherRegex: empty($route->genericParamsCustomRegex->toArray()) ? null : $genericParamsMatchRegex,
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
            dd("here");

            return $this;
        }

        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::ROUTE_NOT_FOUND);
        return $this;
    }
}