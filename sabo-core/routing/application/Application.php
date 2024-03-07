<?php

namespace SaboCore\Routing\Application;

use SaboCore\Config\Config;
use SaboCore\Config\ConfigException;
use SaboCore\Config\EnvConfig;
use SaboCore\Config\FrameworkConfig;

/**
 * @brief Gestionnaire de l'application
 * @author yahaya bathily https://github.com/yahvya/
 */
abstract class Application{
    /**
     * @var Config|null configuration de l'application
     */
    private static ?Config $applicationConfig = null;

    /**
     * @brief Lance l'application
     * @param Config $applicationConfig configuration de l'application
     * @return void
     */
    public static function launchApplication(Config $applicationConfig):void{
        self::$applicationConfig = $applicationConfig;

        try{
            // chargement des fichiers requis et de la configuration
            self::requireNeededFiles();
            // vérification des configurations
            self::checkConfigs();
            // initialisation de la base de données si requise

            // chargement des routes

            // lancement de l'application
        }
        catch(ConfigException $e){

        }
    }

    /**
     * @return Config|null la configuration de l'application ou null si non défini
     */
    public static function getApplicationConfig():?Config{
        return self::$applicationConfig;
    }

    /**
     * @return Config la configuration d'environnement
     * @throws ConfigException en cas de configuration non défini
     */
    public static function getEnvConfig():Config{
        if(self::$applicationConfig) throw new ConfigException("Configuration d'environnement non trouvé");

        return self::$applicationConfig->getConfig("envConfig");
    }

    /**
     * @return Config la configuration du framework
     * @throws ConfigException en cas de configuration non défini
     */
    public static function getFrameworkConfig():Config{
        if(self::$applicationConfig) throw new ConfigException("Configuration de framework non trouvé");

        return self::$applicationConfig->getConfig("frameworkConfig");
    }

    /**
     * @brief Inclus les fichiers requis
     * @return void
     * @throws ConfigException en cas d'erreur
     */
    private static function requireNeededFiles():void{
        require_once(self::$applicationConfig->getConfig("FUNCTIONS_CONFIG_FILEPATH") );

        self::$applicationConfig = Config::create()
            ->setConfig("envConfig",require_once(self::$applicationConfig->getConfig("ENV_CONFIG_FILEPATH") ) )
            ->setConfig("frameworkConfig",require_once(self::$applicationConfig->getConfig("FRAMEWORK_CONFIG_FILEPATH") ) );
    }

    /**
     * @brief Vérifie les configurations
     * @return void
     * @throws ConfigException en cas de configuration mal formée
     */
    private static function checkConfigs():void{
        if(self::$applicationConfig === null) throw new ConfigException("Configuration non défini");

        // vérification de la configuration d'environnement
        $envConfig = self::$applicationConfig->getConfig("envConfig");
        $envConfig->checkConfigs(...array_map(fn(EnvConfig $case):string => $case->value,EnvConfig::cases()));

        // vérification de la configuration du framework
        $frameworkConfig = self::$applicationConfig->getConfig("frameworkConfig");
        $frameworkConfig->checkConfigs(...array_map(fn(FrameworkConfig $case):string => $case->value,FrameworkConfig::cases()));
    }
}