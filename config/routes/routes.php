<?php

use Controller\HomeController;
use Sabo\Sabo\Route;


// routes à placer ici
return Route::generateFrom([
    Route::get("/",[HomeController::class,"home"],"Home:home")
]);
