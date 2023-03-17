<?php

namespace Sabo\Config;

/**
 * énumération contenant la liste des chemins utilisateurs par défaut utilisées par le framework
 */
enum PathConfig:string{
    /**
     * chemin sous dossier des routes séparés
     */
    case ROUTES_SUBFOLER_PATH = "config\\routes\\routes\\";
    /**
     * chemin du fichier route principal
     */
    case MAIN_ROUTE_FILE = "config\\routes\\routes.php";
    /**
     * chemin du fichier de configuration utilisateur du framework
     */
    case SABO_CONFIG_FILEPATH = "config\\sabo\\config.php";
    /**
     * chemin du fichier autoload utilisateur
     */
    case USER_AUTOLOAD_FILEPATH = "app\\vendor\\autoload.php";

    /**
     * défini le chemin du dossier contenant le fichier env
     */
    case ENV_FILEPATH = "config\\";
};