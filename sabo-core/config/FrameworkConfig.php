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
}