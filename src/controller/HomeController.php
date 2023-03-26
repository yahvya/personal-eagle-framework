<?php

namespace Controller;

use Sabo\Controller\Controller\SaboController;

class HomeController extends SaboController{

    public function showHomePage():void{
        $this->render("home/home.twig",[
            "csrf" => $this->generateCsrf()
        ]);
    }

    public function manageForm():void{
        $this->render("home/home.twig",[
            "check" => $this->checkCsrf("csrf"),
            "csrf" => $this->generateCsrf()
        ]);   
    }
}