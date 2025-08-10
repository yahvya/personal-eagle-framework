<?php

namespace Yahvya\EagleFramework\Config;

/**
 * @brief Application configuration enum
 */
enum ApplicationConfig: string
{
    /**
     * @brief Environment config
     * @type Config
     */
    case ENV_CONFIG = "ENV_CONFIG";

    /**
     * @brief Framework config
     * @type Config
     */
    case FRAMEWORK_CONFIG = "FRAMEWORK_CONFIG";
}
