<?php

namespace SaboCore\Utils\Api;

/**
 * @brief Configuration des paramètres d'une requête
 * @author yahaya bathily https://github.com/yahvya
 */
enum SaboApiRequest{
    // méthode de conversion des données

    /**
     * @brief Conversion json
     */
    case JSON_BODY;

    /**
     * @brief Usage du http_build_query
     */
    case HTTP_BUILD_QUERY;

    /**
     * @brief Aucune donnée contenue
     */
    case NO_DATA;

    // mode de récupération du résultat d'une requête

    /**
     * @brief Conservation du résultat sous forme de chaine
     */
    case RESULT_AS_STRING;

    /**
     * @brief Conversion d'un résultat sous forme de chaine json en tableau
     */
    case RESULT_AS_JSON_ARRAY;
}