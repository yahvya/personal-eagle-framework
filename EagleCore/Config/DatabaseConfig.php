<?php

namespace Yahvya\EagleFramework\Config;

/**
 * @brief Database configuration
 */
enum DatabaseConfig: string
{
    /**
     * @brief Define if a connection should be initialized at the start
     * @type boolean
     */
    case INIT_APP_WITH_CONNECTION = "initWithConnection";

    /**
     * @brief Instance provider
     * @type
     */
    case PROVIDER = "provider";

    /**
     * @brief Provider configuration
     * @type Config
     */
    case PROVIDER_CONFIG = "providerConfig";
}