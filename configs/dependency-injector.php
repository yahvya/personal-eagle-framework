<?php


use SaboCore\Application\Application\ApplicationState;
use SaboCore\Routing\Request\Request;

# --------------------------------------------------------------------
# add your custom factories
# --------------------------------------------------------------------

ApplicationState::$injector
    ->factories
    ->set(key: Request::class,value: fn():Request => ApplicationState::$request)

    ;