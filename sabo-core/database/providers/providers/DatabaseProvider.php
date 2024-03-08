<?php

namespace SaboCore\Database\Providers\Providers;

use SaboCore\Config\Config;
use SaboCore\Config\ConfigException;

/**
 * @brief Classe parente de fournisseur d'instance pour le système de base de donnée
 * @author yahaya bathily https://github.com/yahvya/
 */
abstract class DatabaseProvider{
    /**
     * @brief Initialise la base de données
     * @param Config $providerConfig configuration du provider
     * @return void
     * @throws ConfigException en cas d'erreur
     */
    public abstract function initDatabase(Config $providerConfig):void;
}