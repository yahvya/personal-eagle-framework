<?php

use Sabo\Sabo\Route;
use Sabo\Utils\Api\SaboApi;
use Sabo\Utils\Api\SaboApiConfig;

class StripePayment extends SaboApi{
    public function initPayment():void{
        
    }
}


// routes à placer ici
return Route::generateFrom([
    Route::get("/",function():void{
        $stripe = StripePayment::createFromConfig([
            SaboApiConfig::URL->value => "https://stripe.com"
        ]);

        $stripe->initPayment();
    },"")
]);
