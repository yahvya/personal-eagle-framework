<?php

namespace SaboCore\Config;

/**
 * @brief Configuration du framework
 * @author yahaya bathily https://github.com/yahvya/
 */
enum FrameworkConfig:string{
    /**
     * @brief Chemin du dossier public
     * @type string
     */
    case PUBLIC_DIR_PATH = "publicDirPath";

    /**
     * @brief Chemin vers le dossier de stockage
     * @type string
     */
    case STORAGE_DIR_PATH = "storageDirPath";

    /**
     * @brief Liste des extensions de fichiers autorisés à l'accès direct par l'URL en plus de ceux se trouvant dans le dossier public
     * @type string[]
     */
    case AUTHORIZED_EXTENSIONS_AS_PUBLIC = "authorizedExtensionsAsPublic";

    /**
     * @brief Chemin du dossier contenant les routes
     * @type string
     */
    case ROUTES_BASEDIR_PATH = "routesBasedirPath";

    /**
     * @brief Regex de match des paramètres génériques
     * @type string
     * @attention la regex doit capturer le nom de la variable ex: :articleName => :([a-zA-Z]+)
     */
    case ROUTE_GENERIC_PARAMETER_MATCHER = "routeGenericMatcher";
}