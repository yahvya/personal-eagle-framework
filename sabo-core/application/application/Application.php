<?php

namespace SaboCore\Application\Application;

use Exception;
use PhpAddons\ProcedureManager\Procedure;
use SaboCore\Application\ApplicationLaunchProcedure\ApplicationLaunchProcedure;
use SaboCore\Application\ApplicationLaunchProcedure\DatabaseManagementStep;
use SaboCore\Application\ApplicationLaunchProcedure\LoadInitFilesStep;
use SaboCore\Application\ApplicationLaunchProcedure\RoutingStep;
use SaboCore\Configuration\ApplicationConfiguration;
use Throwable;

/**
 * @brief application cycle manager
 */
class Application{
    /**
     * @brief load requirements for web app and launch app
     * @return $this
     * @throws Throwable on the debug mode in case of error
     */
    public function launchWeb():static{
        $launchProcedure = new ApplicationLaunchProcedure(steps: [
            new LoadInitFilesStep,
            new DatabaseManagementStep,
            new RoutingStep
        ]);

        return $this->launchFromProcedure(launchProcedure: $launchProcedure);
    }

    /**
     * @brief load requirements by excluding routing step and launch app
     * @return $this
     * @throws Throwable on the debug mode in case of error
     */
    public function launch():static{
        $launchProcedure = new ApplicationLaunchProcedure(steps: [
            new LoadInitFilesStep,
            new DatabaseManagementStep
        ]);

        return $this->launchFromProcedure(launchProcedure: $launchProcedure);
    }

    /**
     * @brief launch app from the given procedure
     * @param Procedure $launchProcedure launch procedure
     * @return $this
     * @throws Throwable on the debug mode in case of error
     */
    public function launchFromProcedure(Procedure $launchProcedure):static{
        try{
            while(!$launchProcedure->isFinished()){
                if(!$launchProcedure->next()){
                    throw new Exception(message: "Fail to launch app from procedure on step : {$launchProcedure->getCurrentStepNumber()} - step class name : " . get_class(object: $launchProcedure->getCurrentStep()));
                }
            }
        }
        catch(Exception $e){
            ApplicationCycleHooks::call(ApplicationCycle::ERROR_IN_CYCLE,$e);

            # raise exception on the debug mode
            if(function_exists(function: "appEnv") && appEnv(key: ApplicationConfiguration::APPLICATION_DEV_MODE))
                throw $e;
        }
        catch(Throwable $e){
            $exception = new Exception(
                message: $e->getMessage(),
                code: $e->getCode(),
                previous: $e->getPrevious()
            );

            ApplicationCycleHooks::call(ApplicationCycle::ERROR_IN_CYCLE,$exception);

            # raise exception on the debug mode
            if(function_exists(function: "appEnv") && appEnv(key: ApplicationConfiguration::APPLICATION_DEV_MODE))
                throw $e;
        }

        return $this;
    }
}