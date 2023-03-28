<?php

use Controller\HomeController;
use Sabo\Mailer\SaboMailer;
use Sabo\Mailer\SaboMailerConfig;
use Sabo\Sabo\Route;

return Route::generateFrom([
    Route::get("/",function():void{
        $mailer = new SaboMailer("mail de test",[
            SaboMailerConfig::FROM_EMAIL->value => "email@gmail.com",
            SaboMailerConfig::FROM_NAME->value => "jbjbjb"
        ]);

        $mailer->sendMailFromTemplate(["email@gmail.com"],"contenu alt","mail.twig",[
            "var_1" => "test_1",
            "var_2" => "test_2"
        ]);
    },"Home:home")
]);