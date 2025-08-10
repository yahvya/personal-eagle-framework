<?php

namespace Yahvya\EagleFramework\Config;

/**
 * @brief Application path config
 */
enum ApplicationPathConfig: string
{
    /**
     * @brief Environment file config
     * @type string
     */
    case ENV_CONFIG_FILEPATH = "ENV_CONFIG_FILEPATH";

    /**
     * @brief Global functions file path
     * @type string
     */
    case FUNCTIONS_CONFIG_FILEPATH = "FUNCTIONS_CONFIG_FILEPATH";

    /**
     * @brief Chemin du fichier de configuration du framework
     * @type string
     */
    case FRAMEWORK_CONFIG_FILEPATH = "FRAMEWORK_CONFIG_FILEPATH";

    /**
     * @brief Blade configuration file path
     * @type string
     */
    case BLADE_FUNCTIONS_CONFIG_FILEPATH = "BLADE_FUNCTIONS_CONFIG_FILEPATH";

    /**
     * @brief Twig configuration file path
     * @type string
     */
    case TWIG_FUNCTIONS_CONFIG_FILEPATH = "TWIG_FUNCTIONS_CONFIG_FILEPATH";
}
