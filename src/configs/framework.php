<?php

use SaboCore\Config\Config;
use SaboCore\Config\FrameworkConfig;

/**
 * @brief Fichier de configuration global du framework
 * @return Config les variables d'environnement
 */

// placez ici les configurations globales

return Config::create()
    // configurations requises
    ->setConfig(FrameworkConfig::PUBLIC_DIR_PATH->value,"/src/public")
    ->setConfig(FrameworkConfig::STORAGE_DIR_PATH->value,"/src/storage");
