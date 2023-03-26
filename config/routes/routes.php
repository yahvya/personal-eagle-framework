<?php

use Sabo\Sabo\Route;

return Route::generateFrom([
    Route::group("/",[
        Route::get("",function():void{
            echo "je suis bien ici";            
        },"Home@home-page",accessConds:[fn():bool => true])
    ],[fn():bool => true,fn():bool => true])
]);