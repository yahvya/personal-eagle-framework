<?php

use SaboCore\Application\Application\ApplicationCycleHooks;
use SaboCore\Routing\Request\Request;

# --------------------------------------------------------------------
# hooks configuration
# --------------------------------------------------------------------

ApplicationCycleHooks::onMaintenanceBlock(function():void{
    # render maintenance page
});

ApplicationCycleHooks::onErrorInCycle(function(Exception $error):void{
    # render error page

    #dd($error);
});

ApplicationCycleHooks::onRouteNotFounded(function():void{
    # render page not found page
});