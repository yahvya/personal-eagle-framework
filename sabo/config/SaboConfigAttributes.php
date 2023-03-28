<?php

namespace Sabo\Config;

/**
 * clé des configurations possibles
 */
enum SaboConfigAttributes:string{
    /**
     * mode de développement
     */
    case DEBUG_MODE = "debug_mode";
    /**
     * défini si une connexion à la base de données doit être crée au lancement
     */
    case INIT_WITH_DATABASE_CONNEXION = "init_with_database";

    /**
     * défini la page de page non trouvé par défaut
     */
    case NOT_FOUND_DEFAULT_PAGE = "not_found_default_page";

    /**
     * défini la page d'erreur technique par défaut
     */
    case TECHNICAL_ERROR_DEFAULT_PAGE = "technical_error_default_page";

    /**
     * défini le type du fichier de configuration, .env ou .json
     */
    case ENV_FILE_TYPE = "env_file_type";

    /**
     * défini le préfix des données dans un fichier .env
     */
    case BASIC_ENV_FORVIEW_PREFIX = "basic_env_forview_prefix";

    /**
     * chemin du dossier contenant les templates
     */
    case VIEWS_FOLDER_PATH = "views_folder_path";

    /**
     * chemin du dossier contenant les templates de mail
     */
    case MAIL_FOLDER_PATH = "mails_folder_path";
};