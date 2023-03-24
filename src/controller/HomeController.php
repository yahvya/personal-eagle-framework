<?php

namespace Controller;

use Sabo\Controller\Controller\SaboController;

class HomeController extends SaboController{
    public function showHomePage():void{
        $this->render("home/home.twig");
    }
}