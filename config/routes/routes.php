<?php

use Controller\HomeController;
use Sabo\Sabo\Route;

return Route::generateFrom([
    Route::get("/",function():void{
        echo "page d'accueil";
    },"Home:home")
]);