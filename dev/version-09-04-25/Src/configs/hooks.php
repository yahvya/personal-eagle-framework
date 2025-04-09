<?php

use Sabo\Application\Context\Hooks\SaboDefaultHooksHandlers;
use Sabo\Application\Context\Hooks\SaboHooksDto;

# HOOKS CONFIGURATION FILE

/**
 * Configure the hooks manager
 * @param SaboHooksDto $hooks Hooks configuration
 * @attention Do not change this function name
 * @return void
 */
function configureHooks(SaboHooksDto $hooks):void
{
    $hooks->errorInCycle = SaboDefaultHooksHandlers::errorInCycleHandler(...);
}

return true;