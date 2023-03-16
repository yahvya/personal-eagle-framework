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
};