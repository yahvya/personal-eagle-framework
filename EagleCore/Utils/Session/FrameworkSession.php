<?php

namespace Yahvya\EagleFramework\Utils\Session;

/**
 * @brief Framework session storage keys
 */
enum FrameworkSession: string
{
    /**
     * @brief Maintenance access state value
     */
    case MAINTENANCE_ACCESS = "maintenanceAccess";
}