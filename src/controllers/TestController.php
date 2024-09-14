<?php

namespace Controllers;

use SaboCore\Controller\SaboController;
use SaboCore\Routing\Response\JsonResponse;

class TestController extends SaboController {
    public function execute():JsonResponse{
        return new JsonResponse([
           "controllerValue" => "yes"
        ]);
    }
}