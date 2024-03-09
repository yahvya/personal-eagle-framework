<?php

namespace Controllers;

use Override;
use SaboCore\Controller\MaintenanceController;
use SaboCore\Routing\Request\Request;
use SaboCore\Routing\Response\BladeResponse;
use SaboCore\Routing\Response\Response;

class DefaultMaintenanceController extends MaintenanceController{
    #[Override]
    public function showMaintenancePage(string $secretLink): Response{
        return new BladeResponse("maintenance/authentication",[
            "secretLink" => $secretLink
        ]);
    }

    #[Override]
    public function verifyLogin(Request $request): bool{
        debugDie("verification");
        return true;
    }
}