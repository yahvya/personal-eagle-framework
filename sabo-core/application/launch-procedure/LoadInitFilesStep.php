<?php

namespace SaboCore\Application\ApplicationLaunchProcedure;

use PhpAddons\ProcedureManager\Procedure;
use PhpAddons\ProcedureManager\ProcedureStep;
use SaboCore\Application\Application\ApplicationCycle;
use SaboCore\Application\Application\ApplicationCycleHooks;
use SaboCore\PathConfig\AppPathMap;

/**
 * @brief load initialization files
 * @file configs/hooks.php
 * @file configs/functions.php
 * @file configs/env.php
 */
class LoadInitFilesStep implements ProcedureStep {
    public function canAccessNext(Procedure $procedure, ...$args): bool{
        require_once(APP_ROOT . AppPathMap::CONFIGURATIONS_DIRECTORY->value . "/hooks.php");
        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::INIT);

        require_once(APP_ROOT . AppPathMap::CONFIGURATIONS_DIRECTORY->value . "/functions.php");

        $envConfig = require_once(APP_ROOT . AppPathMap::CONFIGURATIONS_DIRECTORY->value . "/env.php");
        
        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::CONFIG_LOADED);

        return true;
    }
}