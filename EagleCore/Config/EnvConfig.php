<?php

namespace Yahvya\EagleFramework\Config;

/**
 * @brief Environment configuration
 */
enum EnvConfig: string
{
    /**
     * @brief Database configuration
     * @type string
     */
    case DATABASE_CONFIG = "database";

    /**
     * @brief Application name
     * @type string
     */
    case APPLICATION_NAME_CONFIG = "applicationName";

    /**
     * @brief Application link
     * @type string
     */
    case APPLICATION_LINK_CONFIG = "applicationLink";

    /**
     * @brief If the application is in the maintenance state
     * @type Config
     */
    case MAINTENANCE_CONFIG = "maintenanceConfig";

    /**
     * @brief If the application is in development mode
     * @type boolean True = yes | False = production or staging
     */
    case DEV_MODE_CONFIG = "devModeConfig";

    /**
     * @brief Mailer config
     * @type Config
     */
    case MAILER_CONFIG = "mailerConfig";
}