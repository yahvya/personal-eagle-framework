<?php

namespace SaboCore\Config;

/**
 * @brief Configuration d'environnement
 * @author yahaya bathily https://github.com/yahvya/
 */
enum EnvConfig:string{
    /**
     * @brief Configuration de base de données
     */
    case DATABASE_CONFIG = "database";

    /**
     * @brief Nom de l'application
     */
    case APPLICATION_NAME_CONFIG = "applicationName";

    /**
     * @brief Lien de l'application
     */
    case APPLICATION_LINK_CONFIG = "applicationLink";
}