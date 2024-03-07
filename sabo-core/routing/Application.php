<?php

namespace SaboCore\Routing;

use SaboCore\Config\Config;
use SaboCore\Config\ConfigException;
use Throwable;

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
     * @var Config|null configuration d'environnement
     */
    private static ?Config $envConfig = null;

    /**
     * @var Config|null configuration du framework
     */
    private static ?Config $frameworkConfig = null;

    /**
     * @brief Lance l'application
     * @param Config $applicationConfig configuration de l'application
     * @return void
     */
    public static function launchApplication(Config $applicationConfig):void{
        self::$applicationConfig = $applicationConfig;

        try{
            self::requireNeededFiles();
            self::checkConfigs();
        }
        catch(ConfigException $e){

        }
        catch(Throwable){

        }
    }

    /**
     * @return Config|null la configuration de l'application
     */
    public static function getApplicationConfig():?Config{
        return self::$applicationConfig;
    }

    /**
     * @return Config|null la configuration d'environnement
     */
    public static function getEnvConfig():?Config{
        return self::$envConfig;
    }

    /**
     * @return Config|null la configuration du framework
     */
    public static function getFrameworkConfig():?Config{
        return self::$frameworkConfig;
    }

    /**
     * @brief Inclus les fichiers requis
     * @return void
     * @throws ConfigException en cas d'erreur
     */
    private static function requireNeededFiles():void{
        require_once(self::$applicationConfig->getConfig("FUNCTIONS_CONFIG_FILEPATH") );
        self::$envConfig = require_once(self::$applicationConfig->getConfig("ENV_CONFIG_FILEPATH") );
        self::$frameworkConfig = require_once(self::$applicationConfig->getConfig("FRAMEWORK_CONFIG_FILEPATH") );
    }

    /**
     * @brief Vérifie les configurations
     * @return void
     */
    private static function checkConfigs():void{

    }
}