<?php

use SaboCore\Application\Application\ApplicationCycleHooks;

# --------------------------------------------------------------------
# hooks configuration
# --------------------------------------------------------------------

ApplicationCycleHooks::onInit(function(){
    # hooks are now loaded #
});

ApplicationCycleHooks::onErrorInCycle(function(Exception $error){
    # do something with the error #
});