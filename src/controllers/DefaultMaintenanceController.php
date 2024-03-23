<?php

namespace Controllers;

use Override;
use SaboCore\Controller\MaintenanceController;
use SaboCore\Routing\Request\Request;
use SaboCore\Routing\Response\BladeResponse;
use SaboCore\Routing\Response\Response;
use SaboCore\Utils\FileManager\FileManager;
use SaboCore\Utils\Storage\AppStorage;
use Throwable;

/**
 * @brief Controller de vérification de maintenance par défaut
 */
class DefaultMaintenanceController extends MaintenanceController{
    #[Override]
    public function showMaintenancePage(string $secretLink): Response{
        return new BladeResponse(
            pathFromViews: "maintenance/authentication",
            datas: [
                "secretLink" => $secretLink
            ]
        );
    }

    #[Override]
    public function verifyLogin(Request $request): bool{
        try{
            ["csrf" => $csrf,"password" => $password] = $request->getPostValues(
                "Accès non autorisé",
                "csrf","password"
            );

            // vérification csrf
            if(!checkCsrf(token: $csrf) ) return false;

            $fileManager = new FileManager(fileAbsolutePath: AppStorage::buildStorageCompletePath(pathFromStorage: "/maintenance/maintenance.secret") );

            // vérification du mot de passe à partir de la clé secrète de maintenance
            return @password_verify(password: $password,hash: $fileManager->getFromStorage()->getContent());
        }
        catch(Throwable){
            return false;
        }
    }
}