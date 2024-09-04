<?php

namespace SaboCore\Application\ApplicationLaunchProcedure;

use PhpAddons\ProcedureManager\Procedure;
use PhpAddons\ProcedureManager\ProcedureStep;
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
        require_once(APP_ROOT . AppPathMap::CONFIGURATIONS_DIRECTORY->value . "/functions.php");
        require_once(APP_ROOT . AppPathMap::CONFIGURATIONS_DIRECTORY->value . "/env.php");

        return true;
    }
}