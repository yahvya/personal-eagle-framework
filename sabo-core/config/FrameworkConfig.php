<?php

namespace SaboCore\Config;

/**
 * @brief Configuration du framework
 * @author yahaya bathily https://github.com/yahvya/
 */
enum FrameworkConfig:string{
    /**
     * @brief Chemin du dossier public
     */
    case PUBLIC_DIR_PATH = "/src/public";

    /**
     * @brief Chemin vers le dossier de stockage
     */
    case STORAGE_DIR_PATH = "/src/storage";
}