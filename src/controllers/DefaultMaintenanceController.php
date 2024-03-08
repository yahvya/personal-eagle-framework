<?php

namespace Controllers;

use Override;
use SaboCore\Controller\MaintenanceController;
use SaboCore\Routing\Request\Request;
use SaboCore\Routing\Response\HtmlResponse;
use SaboCore\Routing\Response\Response;

class DefaultMaintenanceController extends MaintenanceController{
    #[Override]
    public function showMaintenancePage(string $secretLink): Response{
        return new HtmlResponse($secretLink);
    }

    #[Override]
    public function verifyLogin(Request $request): bool{
        return true;
    }
}