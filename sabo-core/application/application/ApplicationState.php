<?php

namespace SaboCore\Application\Application;

use SaboCore\Database\Providers\Connection\ConnectionProvider;
use SaboCore\Routing\Request\Request;

/**
 * @brief application state
 * @attention don't modify the values yourself as possible.
 * @attention preserve the logic of defining static values to allow editors knowing the value type
 */
class ApplicationState{
    /**
     * @var ConnectionProvider|null database connection provider
     */
    public static ?ConnectionProvider $databaseConnectionProvider = null;

    /**
     * @var Request|null request manager
     */
    public static ?Request $request = null;
}