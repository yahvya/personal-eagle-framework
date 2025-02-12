<?php

use Sabo\Application\Context\ApplicationContext;

# HOOKS CONFIGURATION FILE

# manage error in sabo cycle
ApplicationContext::$current->hooks->errorInCycle = function(Exception $exception):void{
    if(ApplicationContext::$current->isInDevMode)
        dd($exception);

    die("An error occurred on the server");
};

return true;