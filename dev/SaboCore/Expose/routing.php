<?php

// ROUTING UTILS

use SaboCore\Core\Http\RequestManager;

/**
 * @return RequestManager A singleton request manager
 */
function request():RequestManager{
    static $requestManager = new RequestManager();

    return $requestManager;
}