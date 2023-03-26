<?php

use Controller\HomeController;
use Middleware\AuthMiddleware;
use Sabo\Middleware\Exception\MiddlewareException;
use Sabo\Sabo\Route;

return Route::generateFrom([
    Route::get("/",[HomeController::class,"showHomePage"],"Home:home_page"),
    Route::post("/form",[HomeController::class,"manageForm"],"Home:form"),
    Route::get("/test",function():void{
        $mdw = new AuthMiddleware();

        try{
            $mdw->auth();
        }
        catch(MiddlewareException $e){
            die($e->getIsDisplayable() ? $e->getMessage() : "erreur non affichable");
        }
    },"Mdw")
]);