<?php

namespace Controller;

use Sabo\Controller\Controller\SaboController;

/**
 * controlleur de la page d'accueil
 * @name HomeController
 */
class HomeController extends SaboController{
    public function home():void{
        $this->render("home/home.twig");
    }
}