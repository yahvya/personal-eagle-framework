<?php

namespace SaboCore\Application\ApplicationLaunchProcedure;

use PhpAddons\ProcedureManager\Procedure;
use PhpAddons\ProcedureManager\ProcedureStep;
use SaboCore\Application\Application\ApplicationCycle;
use SaboCore\Application\Application\ApplicationCycleHooks;
use SaboCore\Configuration\ApplicationConfiguration;
use SaboCore\Configuration\DatabaseConfiguration;
use SaboCore\Configuration\EnvConfig;
use SaboCore\Configuration\MaintenanceConfiguration;
use SaboCore\PathConfig\AppPathMap;
use SaboCore\Utils\Verification\ArrayVerifier;

/**
 * @brief load initialization files
 * @file configs/hooks.php
 * @file configs/functions.php
 * @file configs/env.php
 */
class LoadInitFilesStep implements ProcedureStep {
    public function canAccessNext(Procedure $procedure, ...$args): bool{
        # loading hooks
        require_once(APP_ROOT . AppPathMap::CONFIGURATIONS_DIRECTORY->value . "/hooks.php");
        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::INIT);

        # loading global functions
        require_once(APP_ROOT . AppPathMap::CONFIGURATIONS_DIRECTORY->value . "/functions.php");

        # loading and affecting $_ENV
        $envConfig = require_once(APP_ROOT . AppPathMap::CONFIGURATIONS_DIRECTORY->value . "/env.php");

        if(!$this->loadEnvFrom(envConfig: $envConfig))
            return false;

        # loading routes
        if(!$this->loadRoutes())
            return false;

        # loading success
        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::CONFIG_LOADED);
        return true;
    }

    /**
     * @brief verify env configs and affect in $_ENV
     * @param array $envConfig env configuration
     * @return bool if configuration succeed
     */
    protected function loadEnvFrom(array $envConfig): bool{
        # verify configuration required keys

        $keysToVerify = [
            # application config
            EnvConfig::APPLICATION_CONFIGURATION . "." . ApplicationConfiguration::APPLICATION_DEV_MODE,
            EnvConfig::APPLICATION_CONFIGURATION . "." . ApplicationConfiguration::APPLICATION_LINK,
            EnvConfig::APPLICATION_CONFIGURATION . "." . ApplicationConfiguration::APPLICATION_NAME,
            EnvConfig::APPLICATION_CONFIGURATION . "." . ApplicationConfiguration::APPLICATION_VERSION,
            EnvConfig::APPLICATION_CONFIGURATION . "." . ApplicationConfiguration::APPLICATION_ASSETS_VERSION,

            # database config
            EnvConfig::DATABASE_CONFIGURATION . "." . DatabaseConfiguration::INIT_APP_WITH_CON,

            # maintenance config
            EnvConfig::MAINTENANCE_CONFIGURATION . "." . MaintenanceConfiguration::IS_IN_MAINTENANCE,
            EnvConfig::MAINTENANCE_CONFIGURATION . "." . MaintenanceConfiguration::ACCESS_CODE,
            EnvConfig::MAINTENANCE_CONFIGURATION . "." . MaintenanceConfiguration::MAX_TRY,
            EnvConfig::MAINTENANCE_CONFIGURATION . "." . MaintenanceConfiguration::SECRET_ACCESS_LINK,
            EnvConfig::MAINTENANCE_CONFIGURATION . "." . MaintenanceConfiguration::GET_VARIABLE_NAME,
        ];

        $verifier = new ArrayVerifier(toVerify: $envConfig);

        if(!$verifier->verifyKeys(keys: $keysToVerify))
            return false;

        # verify configuration required keys on condition
        if(
            $envConfig[EnvConfig::DATABASE_CONFIGURATION][DatabaseConfiguration::INIT_APP_WITH_CON] &&
            !$verifier->verifyKeys(keys: [EnvConfig::DATABASE_CONFIGURATION . "." . DatabaseConfiguration::CONNECTION_PROVIDER])
        )
            return false;

        # affecting keys
        $_ENV = $envConfig;

        return true;
    }

    /**
     * @brief load app routes
     * @return bool if load succeed
     */
    protected function loadRoutes():bool{
        $routesDir = APP_ROOT . AppPathMap::ROUTES_DIRECTORY->value;
        $routesDirFiles = @scandir(directory: $routesDir);

        if($routesDirFiles === false)
            return false;

        $routesDirFiles = array_diff($routesDirFiles,[".",".."]);

        foreach($routesDirFiles as $filename)
            require_once("$routesDir/$filename");

        return true;
    }
}