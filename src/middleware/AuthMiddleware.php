<?php

namespace Middleware;

use Sabo\Middleware\Middleware\SaboMiddleware;

class AuthMiddleware extends SaboMiddleware{
    public function auth():void{
        $this->throwException("Ma nouvelle exception",false);
    }
}