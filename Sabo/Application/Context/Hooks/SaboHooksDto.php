<?php

namespace Sabo\Application\Context\Hooks;

use Closure;

/**
 * Sabo cycle hooks
 */
class SaboHooksDto
{
    /**
     * @param Closure|null $errorInCycle When an error occurred in the cycle (Exception)
     * @param Closure|null $onHooksLoaded When hooks finished to load
     * @param Closure|null $onDependencyInjectorLoaded When dependency injector finish to load
     * @param Closure|null $onGlobalFunctionsLoaded When global functions finish to load
     * @param Closure|null $onEnvironmentLoaded When environment finish to load
     * @param Closure|null $onDatabaseLoaded When database finish to load
     * @param Closure|null $onMaintenanceBlock When user is block during the maintenance check
     * @param Closure|null $onMaintenanceCheckPass When the maintenance check if passed
     * @param Closure|null $onRouteFound When the searched route is found
     * @param Closure|null $onRouteNotFound When route not found
     * @param Closure|null $onRouteAccessCondFail When route access cond fail
     * @param Closure|null $beforeResponseRender Before response render
     */
    public function __construct(
        public ?Closure $errorInCycle = null,
        public ?Closure $onHooksLoaded = null,
        public ?Closure $onDependencyInjectorLoaded = null,
        public ?Closure $onGlobalFunctionsLoaded = null,
        public ?Closure $onEnvironmentLoaded = null,
        public ?Closure $onDatabaseLoaded = null,
        public ?Closure $onMaintenanceBlock = null,
        public ?Closure $onMaintenanceCheckPass = null,
        public ?Closure $onRouteFound = null,
        public ?Closure $onRouteNotFound = null,
        public ?Closure $onRouteAccessCondFail = null,
        public ?Closure $beforeResponseRender = null
    ){}
}