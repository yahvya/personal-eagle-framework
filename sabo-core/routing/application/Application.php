<?php

namespace SaboCore\Routing\Application;

use SaboCore\Config\ApplicationConfig;
use SaboCore\Config\ApplicationPathConfig;
use SaboCore\Config\Config;
use SaboCore\Config\ConfigException;
use SaboCore\Config\DatabaseConfig;
use SaboCore\Config\EnvConfig;
use SaboCore\Config\FrameworkConfig;
use SaboCore\Config\MaintenanceConfig;
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
    protected static ?Config $applicationConfig = null;

    /**
     * @brief Lance l'application
     * @param Config $applicationConfig configuration de l'application
     * @@param bool $startRouting si la recherche et le rendu de routing doit être fait
     * @return void
     */
    public static function launchApplication(Config $applicationConfig,bool $startRouting = true):void{
        self::$applicationConfig = $applicationConfig;

        try{
            // chargement des fichiers requis et de la configuration
            self::requireNeededFiles();
            // vérification des configurations
            self::checkConfigs();

            // chargement des routes
            require_once(
                APP_CONFIG->getConfig(name: "ROOT") .
                Application::getFrameworkConfig()->getConfig(name: FrameworkConfig::ROUTES_BASEDIR_PATH->value) .
                "/routes.php"
            );

            try{
                // initialisation de la base de données si requise
                self::initDatabase();

                // lancement de l'application
                if($startRouting){
                    $routingManager = new RoutingManager();
                    $routingManager
                        ->start()
                        ->renderResponse();
                }
            }
            catch(Throwable $e){
                // vérification si mode debug
                if(
                    self::$applicationConfig
                        ->getConfig(name:ApplicationConfig::ENV_CONFIG->value)
                        ->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value)
                )
                    debugDie($e);
                else
                    throw $e;
            }
        }
        catch(Throwable) {
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
        if(self::$applicationConfig === null) throw new ConfigException("Configuration d'environnement non trouvé");

        return self::$applicationConfig->getConfig(ApplicationConfig::ENV_CONFIG->value);
    }

    /**
     * @return Config la configuration du framework
     * @throws ConfigException en cas de configuration non défini
     */
    public static function getFrameworkConfig():Config{
        if(self::$applicationConfig === null) throw new ConfigException("Configuration de framework non trouvé");

        return self::$applicationConfig->getConfig(ApplicationConfig::FRAMEWORK_CONFIG->value);
    }

    /**
     * @brief
     * @param Config $envConfig nouvelle configuration de l'environnement
     * @return void
     * @throws ConfigException en cas de configuration non prédéfini
     */
    public static function setEnvConfig(Config $envConfig):void{
        if(self::$applicationConfig === null)
            throw new ConfigException(message: "L'application n'a pas été configuré");

        self::$applicationConfig->setConfig(name: ApplicationConfig::ENV_CONFIG->value,value: $envConfig);
    }

    /**
     * @return Config la configuration par défaut de l'application
     */
    public static function getApplicationDefaultConfig():Config{
        $appRoot = __DIR__ . "/../../..";

        return Config::create()
            // configurations des chemins
            ->setConfig(
                name: ApplicationPathConfig::ENV_CONFIG_FILEPATH->value,
                value: "$appRoot/src/configs/env.php"
            )
            ->setConfig(
                name: ApplicationPathConfig::FUNCTIONS_CONFIG_FILEPATH->value,
                value: "$appRoot/src/configs/functions.php"
            )
            ->setConfig(
                name: ApplicationPathConfig::FRAMEWORK_CONFIG_FILEPATH->value,
                value: "$appRoot/src/configs/framework.php"
            )
            ->setConfig(
                name: ApplicationPathConfig::BLADE_FUNCTIONS_CONFIG_FILEPATH->value,
                value: "$appRoot/src/configs/blade-config.php"
            )
            ->setConfig(
                name: ApplicationPathConfig::TWIG_FUNCTIONS_CONFIG_FILEPATH->value,
                value: "$appRoot/src/configs/twig-config.php"
            );
    }

    /**
     * @brief Inclus les fichiers requis
     * @return void
     * @throws ConfigException en cas d'erreur
     */
    protected static function requireNeededFiles():void{
        require_once(self::$applicationConfig->getConfig(name: ApplicationPathConfig::FUNCTIONS_CONFIG_FILEPATH->value));
        require_once(self::$applicationConfig->getConfig(name: ApplicationPathConfig::BLADE_FUNCTIONS_CONFIG_FILEPATH->value));
        require_once(self::$applicationConfig->getConfig(name: ApplicationPathConfig::TWIG_FUNCTIONS_CONFIG_FILEPATH->value));

        self::$applicationConfig = Config::create()
            ->setConfig(
                name: ApplicationConfig::ENV_CONFIG->value,
                value: require_once(self::$applicationConfig->getConfig(name: ApplicationPathConfig::ENV_CONFIG_FILEPATH->value)))
            ->setConfig(
                name: ApplicationConfig::FRAMEWORK_CONFIG->value,
                value: require_once(self::$applicationConfig->getConfig(name: ApplicationPathConfig::FRAMEWORK_CONFIG_FILEPATH->value)));
    }

    /**
     * @brief Vérifie les configurations
     * @return void
     * @throws ConfigException en cas de configuration mal formée
     */
    protected static function checkConfigs():void{
        if(self::$applicationConfig === null)
            throw new ConfigException(message: "Configuration non défini");

        // vérification de la configuration d'environnement
        $envConfig = self::$applicationConfig->getConfig(name: ApplicationConfig::ENV_CONFIG->value);
        $envConfig->checkConfigs(...array_map(fn(EnvConfig $case):string => $case->value,EnvConfig::cases()));

        // vérification de la configuration du framework
        $frameworkConfig = self::$applicationConfig->getConfig(name: ApplicationConfig::FRAMEWORK_CONFIG->value);
        $frameworkConfig->checkConfigs(...array_map(fn(FrameworkConfig $case):string => $case->value,FrameworkConfig::cases()));

        // vérification de la configuration de maintenance
        $maintenanceConfig = $envConfig->getConfig(name: EnvConfig::MAINTENANCE_CONFIG->value);
        $maintenanceConfig->checkConfigs(...array_map(fn(MaintenanceConfig $case):string => $case->value,MaintenanceConfig::cases()));
    }

    /**
     * @brief Initialise la base de données si requise
     * @return void
     * @throws ConfigException en cas d'erreur
     */
    protected static function initDatabase():void{
        $databaseConfig = self::$applicationConfig
            ->getConfig(name: ApplicationConfig::ENV_CONFIG->value)
            ->getConfig(name: EnvConfig::DATABASE_CONFIG->value);

        // vérification du choix d'initialisation de base de donnée ou non
        if(!$databaseConfig->getConfig(name: DatabaseConfig::INIT_APP_WITH_CONNECTION->value) ) return;

        // vérification de la présence de chaque élement de configuration
        $databaseConfig->checkConfigs(...array_map(fn(DatabaseConfig $case):string => $case->value,DatabaseConfig::cases()));

        // initialisation de la base de données
        $databaseConfig
            ->getConfig(name: DatabaseConfig::PROVIDER->value)
            ->initDatabase(providerConfig: $databaseConfig->getConfig(name: DatabaseConfig::PROVIDER_CONFIG->value));
    }

    /**
     * @brief Affiche la page de page non trouvée
     * @return void
     */
    protected static function showInternalErrorPage():void{
        try{
            // affichage de la page d'erreur
            $response = new HtmlResponse(
                content: @file_get_contents(APP_CONFIG->getConfig("ROOT") . "/src/views/default-pages/internal-error.html") ??
                "Erreur interne"
            );

            $response
                ->setResponseCode(code: ResponseCode::INTERNAL_SERVER_ERROR)
                ->renderResponse();
        }
        catch(Throwable){}
    }
}