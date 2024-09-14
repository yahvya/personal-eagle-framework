<?php


use SaboCore\Application\Application\ApplicationState;
use SaboCore\Controller\SaboController;
use SaboCore\Routing\Request\Request;
use SaboCore\Utils\Injection\Injector\DependencyInjector;

# --------------------------------------------------------------------
# add your custom factories
# --------------------------------------------------------------------

ApplicationState::$injector
    ->factories

    ;

# --------------------------------------------------------------------
# framework default factories
# --------------------------------------------------------------------

ApplicationState::$injector
    ->addClassSubTypesFactories(class: SaboController::class)
    ->factories
        ->set(key: Request::class,value: fn():Request => ApplicationState::$request)
        ->set(key: DependencyInjector::class,value: fn():DependencyInjector => ApplicationState::$injector)
    ;