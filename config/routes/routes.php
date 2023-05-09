<?php

use Sabo\Sabo\Route;
use Sabo\Utils\Api\SaboApi;
use Sabo\Utils\Api\SaboApiConfig;

class PaypalPayment extends SaboApi{
    public function initPayment():void{
        
    }
}


// routes à placer ici
return Route::generateFrom([
    Route::get("/",function():void{
        $stripe = PaypalPayment::createFromConfig([
            SaboApiConfig::URL->value => "https://stripe.com"
        ]);

        $stripe->initPayment();
    },"")
]);
