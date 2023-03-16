<?php

use Sabo\Helper\Regex;
use Sabo\Sabo\Route;

return Route::generateFrom([
    Route::get("/",function():void{
        echo "bienvenue sur le site";
    },"Home:home_page"),
    Route::get("/article/{articleId}/",function(int $articleId):void{
        echo "l'article cherché est {$articleId}";
    },"Article:show_an_article",["articleId" => Regex::intRegex()])
]);