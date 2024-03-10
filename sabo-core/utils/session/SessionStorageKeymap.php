<?php

namespace SaboCore\Utils\Session;

/**
 * @brief Clés de session
 * @author yahaya bathily https://github.com/yahvya
 */
enum SessionStorageKeymap:string{
    /**
     * @brief Clé pour les valeurs stockées par l'utilisateur
     */
    case FOR_USER = "FOR_USER";

    /**
     * @brief Clé pour les valeurs des données flash
     */
    case FOR_FLASH = "FOR_FLASH";

    /**
     * @brief Clé pour les valeurs réservées au framework
     */
    case FOR_FRAMEWORK = "FOR_FRAMEWORK";

    /**
     * @brief Clé pour les tokens csrf
     */
    case FOR_CSRF_TOKEN = "FOR_CSRF_TOKEN";
}
