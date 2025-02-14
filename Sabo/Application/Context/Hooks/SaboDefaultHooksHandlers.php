<?php

namespace Sabo\Application\Context\Hooks;

use Exception;
use Sabo\Application\Context\Application\ApplicationContext;

/**
 * Sabo hooks events default handlers
 */
abstract class SaboDefaultHooksHandlers
{
    /**
     * Error in cycle hook handler
     * @param Exception $exception $exception
     * @attention If you want to replace the default handler please implement your own
     * @return never
     */
    public static function errorInCycleHandler(Exception $exception):never{
        if(ApplicationContext::$current->isInDevMode)
            dd($exception);

        die("An error occurred on the server");
    }
}