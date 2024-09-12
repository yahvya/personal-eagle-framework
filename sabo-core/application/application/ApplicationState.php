<?php

namespace SaboCore\Application\Application;

use SaboCore\Database\Providers\Connection\ConnectionProvider;
use SaboCore\Routing\Request\Request;
use SaboCore\Utils\Injection\Injector\DependencyInjector;

/**
 * @brief application state
 * @attention don't modify the values yourself as possible.
 * @attention preserve the logic of defining static values to allow editors knowing the value type
 */
class ApplicationState{
    /**
     * @var ConnectionProvider|null database connection provider
     * @attention initialized for the database management state
     */
    public static ?ConnectionProvider $databaseConnectionProvider = null;

    /**
     * @var Request|null request manager
     * @attention initialized at routing step
     */
    public static ?Request $request = null;

    /**
     * @var DependencyInjector|null app injector
     * @attention initialize from the init method
     */
    public static ?DependencyInjector $injector = null;

    /**
     * @return void initialize the global state
     */
    public static function init():void{
        static::initInjector();
    }

    /**
     * @brief init the dependency injector
     * @return void
     */
    protected static function initInjector():void{
        # init injector
        static::$injector = new DependencyInjector();
    }
}