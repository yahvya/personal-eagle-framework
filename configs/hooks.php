<?php

use SaboCore\Application\Application\ApplicationCycleHooks;

# --------------------------------------------------------------------
# hooks configuration
# --------------------------------------------------------------------

ApplicationCycleHooks::onMaintenanceBlock(function():void{
    # render maintenance page

    echo "maintenance bloqué";
});

ApplicationCycleHooks::onErrorInCycle(function(Exception $error):void{
    # render error page

    dd($error);
});

ApplicationCycleHooks::onRouteNotFounded(function():void{
    # render page not found page

    echo "page non trouvée";
});