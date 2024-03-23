<?php

namespace SaboCore\Config;

/**
 * @brief Configuration des chemins de l'application
 * @author yahaya bathily https://github.com/yahvya
 */
enum ApplicationPathConfig:string{
    /**
     * @brief Chemin du fichier d'environnement
     * @type string
     */
    case ENV_CONFIG_FILEPATH = "ENV_CONFIG_FILEPATH";

    /**
     * @brief Chemin du fichier des fonctions globales
     * @type string
     */
    case FUNCTIONS_CONFIG_FILEPATH = "FUNCTIONS_CONFIG_FILEPATH";

    /**
     * @brief Chemin du fichier de configuration du framework
     * @type string
     */
    case FRAMEWORK_CONFIG_FILEPATH = "FRAMEWORK_CONFIG_FILEPATH";

    /**
     * @brief Chemin du fichier de configuration blade
     * @type string
     */
    case BLADE_FUNCTIONS_CONFIG_FILEPATH = "BLADE_FUNCTIONS_CONFIG_FILEPATH";

    /**
     * @brief Chemin du fichier de configuration twig
     * @type string
     */
    case TWIG_FUNCTIONS_CONFIG_FILEPATH = "TWIG_FUNCTIONS_CONFIG_FILEPATH";
}
