<?php

namespace Application\Controllers;

use Override;
use Yahvya\EagleFramework\Controller\MaintenanceController;
use Yahvya\EagleFramework\Routing\Request\Request;
use Yahvya\EagleFramework\Routing\Response\BladeResponse;
use Yahvya\EagleFramework\Routing\Response\Response;
use Yahvya\EagleFramework\Utils\FileManager\FileManager;
use Yahvya\EagleFramework\Utils\Storage\AppStorage;
use Throwable;

/**
 * @brief Maintenance check default controller
 */
class DefaultMaintenanceController extends MaintenanceController
{
    #[Override]
    public function showMaintenancePage(string $secretLink): Response
    {
        return new BladeResponse(
            pathFromViews: "maintenance/authentication",
            datas: [
                "secretLink" => $secretLink
            ]
        );
    }

    #[Override]
    public function verifyLogin(Request $request): bool
    {
        try
        {
            ["csrf" => $csrf, "password" => $password] = $request->getPostValues(
                "Accès non autorisé",
                "csrf", "password"
            );

            if (!checkCsrf(token: $csrf))
                return false;

            $fileManager = new FileManager(fileAbsolutePath: AppStorage::buildStorageCompletePath(pathFromStorage: "/maintenance/maintenance.secret"));

            return @password_verify(password: $password, hash: $fileManager->getFromStorage()->getContent());
        }
        catch (Throwable)
        {
            return false;
        }
    }
}