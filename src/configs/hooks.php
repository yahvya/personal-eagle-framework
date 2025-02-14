<?php

use Sabo\Application\Context\Application\ApplicationContext;
use Sabo\Application\Context\Hooks\SaboDefaultHooksHandlers;

# HOOKS CONFIGURATION FILE

# manage error in sabo cycle
ApplicationContext::$current->hooks->errorInCycle = SaboDefaultHooksHandlers::errorInCycleHandler(...);

return true;