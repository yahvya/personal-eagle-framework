<?php

namespace SaboCore\Routing\Application;

use SaboCore\Config\Config;
use SaboCore\Config\ConfigException;
use SaboCore\Config\DatabaseConfig;
use SaboCore\Config\EnvConfig;
use SaboCore\Config\FrameworkConfig;
use SaboCore\Routing\Response\HtmlResponse;
use SaboCore\Routing\Response\ResponseCode;
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

            try{
                // initialisation de la base de données si requise
                self::initDatabase();
                // chargement des routes

                // vérification de maintenance

                // lancement de l'application
                debugDie(parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH) );
            }
            catch(ConfigException $e){
                if(self::$applicationConfig->getConfig("ENV_CONFIG")->getConfig(EnvConfig::DEV_MODE_CONFIG->value) )
                    debugDie($e);
                else
                    throw $e;
            }
        }
        catch(ConfigException) {
            self::showInternalErrorPage();
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

        return self::$applicationConfig->getConfig("ENV_CONFIG");
    }

    /**
     * @return Config la configuration du framework
     * @throws ConfigException en cas de configuration non défini
     */
    public static function getFrameworkConfig():Config{
        if(self::$applicationConfig) throw new ConfigException("Configuration de framework non trouvé");

        return self::$applicationConfig->getConfig("FRAMEWORK_CONFIG");
    }

    /**
     * @brief Inclus les fichiers requis
     * @return void
     * @throws ConfigException en cas d'erreur
     */
    private static function requireNeededFiles():void{
        require_once(self::$applicationConfig->getConfig("FUNCTIONS_CONFIG_FILEPATH") );

        self::$applicationConfig = Config::create()
            ->setConfig("ENV_CONFIG",require_once(self::$applicationConfig->getConfig("ENV_CONFIG_FILEPATH") ) )
            ->setConfig("FRAMEWORK_CONFIG",require_once(self::$applicationConfig->getConfig("FRAMEWORK_CONFIG_FILEPATH") ) );
    }

    /**
     * @brief Vérifie les configurations
     * @return void
     * @throws ConfigException en cas de configuration mal formée
     */
    private static function checkConfigs():void{
        if(self::$applicationConfig === null) throw new ConfigException("Configuration non défini");

        // vérification de la configuration d'environnement
        $envConfig = self::$applicationConfig->getConfig("ENV_CONFIG");
        $envConfig->checkConfigs(...array_map(fn(EnvConfig $case):string => $case->value,EnvConfig::cases()));

        // vérification de la configuration du framework
        $frameworkConfig = self::$applicationConfig->getConfig("FRAMEWORK_CONFIG");
        $frameworkConfig->checkConfigs(...array_map(fn(FrameworkConfig $case):string => $case->value,FrameworkConfig::cases()));
    }

    /**
     * @brief Initialise la base de données si requise
     * @return void
     * @throws ConfigException en cas d'erreur
     */
    private static function initDatabase():void{
        $databaseConfig = self::$applicationConfig
            ->getConfig("ENV_CONFIG")
            ->getConfig(EnvConfig::DATABASE_CONFIG->value);

        if(!$databaseConfig->getConfig(DatabaseConfig::INIT_APP_WITH_CONNECTION->value) ) return;

        // vérification de la présence de chaque élement de configuration
        $databaseConfig->checkConfigs(...array_map(fn(DatabaseConfig $case):string => $case->value,DatabaseConfig::cases()));

        // initialisation de la base de données
        $databaseConfig
            ->getConfig(DatabaseConfig::PROVIDER->value)
            ->initDatabase($databaseConfig->getConfig(DatabaseConfig::PROVIDER_CONFIG->value));
    }

    /**
     * @brief Affiche la page de page non trouvée
     * @return void
     */
    private static function showInternalErrorPage():void{
        try{
            // affichage de la page d'erreur
            $response = new HtmlResponse(
                @file_get_contents(APP_CONFIG->getConfig("ROOT") . "/src/views/default-pages/internal-error.html") ??
                "Erreur interne"
            );

            $response
                ->setResponseCode(ResponseCode::INTERNAL_SERVER_ERROR)
                ->renderResponse();
        }
        catch(Throwable){}
    }
}