<?php

namespace SaboCore\Config;

/**
 * @brief Configuration de la base de donnée
 * @author yahaya bathily https://github.com/yahvya/
 */
enum DatabaseConfig:string{
    /**
     * @brief Défini si une connexion à la base de données doit être initialisé
     * @type boolean
     */
    case INIT_APP_WITH_CONNECTION = "initWithConnection";

    /**
     * @brief Fournisseur d'instance
     * @type
     */
    case PROVIDER = "provider";

    /**
     * @brief Configuration du fournisseur d'instance
     * @type Config
     */
    case PROVIDER_CONFIG = "providerConfig";
}