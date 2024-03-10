<?php

namespace SaboCore\Config;

/**
 * @brief Configuration d'environnement
 * @author yahaya bathily https://github.com/yahvya/
 */
enum EnvConfig:string{
    /**
     * @brief Configuration de base de données
     * @type string
     */
    case DATABASE_CONFIG = "database";

    /**
     * @brief Nom de l'application
     * @type string
     */
    case APPLICATION_NAME_CONFIG = "applicationName";

    /**
     * @brief Lien de l'application
     * @type string
     */
    case APPLICATION_LINK_CONFIG = "applicationLink";

    /**
     * @brief Si l'application est en maintenance
     * @type Config
     */
    case MAINTENANCE_CONFIG = "maintenanceConfig";

    /**
     * @brief Si l'application est en mode développement
     * @type boolean true oui sinon production
     */
    case DEV_MODE_CONFIG = "devModeConfig";

    /**
     * @brief Configuration de l'envoi de mail
     * @type Config
     */
    case MAILER_CONFIG = "mailerConfig";
}