<?php

namespace Yahvya\EagleFramework\Controller;

use Yahvya\EagleFramework\Config\ConfigException;
use Yahvya\EagleFramework\Routing\Request\Request;
use Yahvya\EagleFramework\Routing\Response\Response;

/**
 * @brief Abstract maintenance controller
 */
abstract class MaintenanceController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @brief Show the authentication page
     * @param string $secretLink Maintenance secret link
     * @return Response Manager response
     * @throws ConfigException On error
     */
    abstract public function showMaintenancePage(string $secretLink): Response;

    /**
     * @brief Check the access to the website
     * @param Request $request Request data
     * @return bool If the access is authorized
     */
    public abstract function verifyLogin(Request $request): bool;
}