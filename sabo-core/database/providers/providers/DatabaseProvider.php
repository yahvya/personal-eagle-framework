<?php

namespace SaboCore\Database\Providers\Providers;

use SaboCore\Config\Config;

/**
 * @brief Classe parente de fournisseur d'instance pour le système de base de donnée
 * @author yahaya bathily https://github.com/yahvya/
 */
abstract class DatabaseProvider{
    public abstract function initDatabase(Config $providerConfig):void;
}