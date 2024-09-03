<?php

/**
 * @brief Définissez les évènements à capturer durant le cycle de vie
 */

use SaboCore\Routing\Application\ApplicationCycleHooks;

ApplicationCycleHooks::onInit(action: function(){
    // action à faire au lancement
});