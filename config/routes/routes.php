<?php

use Controller\HomeController;
use Sabo\Sabo\Route;

return Route::generateFrom([
    Route::get("/",[HomeController::class,"showHomePage"],"Home:home_page"),
    Route::post("/form",[HomeController::class,"manageForm"],"Home:form")
]);