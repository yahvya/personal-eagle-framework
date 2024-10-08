<?php

namespace SaboCore\Application\ApplicationLaunchProcedure;

use Override;
use PhpAddons\ProcedureManager\Procedure;
use PhpAddons\ProcedureManager\ProcedureStep;
use SaboCore\Application\Application\ApplicationCycle;
use SaboCore\Application\Application\ApplicationCycleHooks;
use SaboCore\Application\Application\ApplicationState;
use SaboCore\Configuration\DatabaseConfiguration;

/**
 * @brief manage database requirements
 */
class DatabaseManagementStep implements ProcedureStep{
    #[Override]
    public function canAccessNext(Procedure $procedure, ...$args): bool{
        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::BEFORE_DATABASE_INIT);

        # check if a database connection have to be opened
        if(!dbEnv(key: DatabaseConfiguration::INIT_APP_WITH_CON)){
            ApplicationCycleHooks::call(cycleStep: ApplicationCycle::AFTER_DATABASE_INIT);
            return true;
        }

        # save the provider on app state
        ApplicationState::$databaseConnectionProvider = dbEnv(key: DatabaseConfiguration::CONNECTION_PROVIDER);

        if(ApplicationState::$databaseConnectionProvider === null)
            return false;

        ApplicationCycleHooks::call(cycleStep: ApplicationCycle::AFTER_DATABASE_INIT);
        return true;
    }
}