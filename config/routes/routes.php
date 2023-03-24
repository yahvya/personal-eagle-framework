<?php

use Controller\HomeController;
use Sabo\Sabo\Route;

return Route::generateFrom([
    Route::get("/{lang}",[HomeController::class,"showHomePage"],"Home:home_page")
]);