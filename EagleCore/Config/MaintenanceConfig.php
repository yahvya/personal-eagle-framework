<?php

namespace Yahvya\EagleFramework\Config;

/**
 * @brief Maintenance configuration
 */
enum MaintenanceConfig: string
{
    /**
     * @brief If the website is in maintenance
     * @type boolean
     */
    case IS_IN_MAINTENANCE = "isInMaintenance";

    /**
     * @brief Secret access link
     * @type string
     */
    case SECRET_LINK = "secretLink";

    /**
     * @brief Maintenance access php class
     * @type string
     */
    case ACCESS_MANAGER = "accessManager";
}