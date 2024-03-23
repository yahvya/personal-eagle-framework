<?php

namespace SaboCore\Config;

/**
 * @brief Enumération de configuration de l'application
 * @author yahaya bathily https://github.com/yahvya
 */
enum ApplicationConfig:string{
    /**
     * @brief Configuration de l'environnement
     * @type Config
     */
    case ENV_CONFIG = "ENV_CONFIG";

    /**
     * @brief Configuration du framework
     * @type Config
     */
    case FRAMEWORK_CONFIG = "FRAMEWORK_CONFIG";
}
