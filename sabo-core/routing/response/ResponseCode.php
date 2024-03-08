<?php

namespace SaboCore\Routing\Response;

/**
 * @brief Codes retours http
 * @author yahaya bathily https://github.com/yahvya
 */
enum ResponseCode:int{
    /**
     * @brief Succès de la requête.
     */
    case OK = 200;

    /**
     * @brief La requête a été traitée avec succès et a entraîné la création d'une ressource.
     */
    case CREATED = 201;

    /**
     * @brief La réponse est vide (pas de contenu à renvoyer).
     */
    case NO_CONTENT = 204;

    /**
     * @brief La syntaxe de la requête est erronée.
     */
    case BAD_REQUEST = 400;

    /**
     * @brief L'accès à la ressource est refusé en raison d'informations d'identification invalides.
     */
    case UNAUTHORIZED = 401;

    /**
     * @brief Le serveur a compris la requête, mais refuse de la traiter.
     */
    case FORBIDDEN = 403;

    /**
     * @brief La ressource demandée n'a pas été trouvée sur le serveur.
     */
    case NOT_FOUND = 404;

    /**
     * @brief Erreur interne du serveur.
     */
    case INTERNAL_SERVER_ERROR = 500;
}