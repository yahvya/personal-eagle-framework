<?php

namespace Sabo\Utils\Api;

/**
 * configuration des paramètres d'une requête
 */
enum SaboApiRequest{
    // méthode de conversion des données
    case JSON_BODY;
    case HTTP_BUILD_QUERY; 
    case NO_DATA;

    // mode de récupération du résultat d'une requête
    case RESULT_AS_STRING;
    case RESULT_AS_JSON_ARRAY;
}