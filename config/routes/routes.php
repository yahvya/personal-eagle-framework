<?php

use Controller\HomeController;
use Sabo\Mailer\SaboMailer;
use Sabo\Mailer\SaboMailerConfig;
use Sabo\Sabo\Route;

return Route::generateFrom([
    Route::get("/",function():void{
        
    },"Home:home")
]);