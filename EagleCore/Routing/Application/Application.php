<?php

namespace Yahvya\EagleFramework\Routing\Application;

use Yahvya\EagleFramework\Config\ApplicationConfig;
use Yahvya\EagleFramework\Config\ApplicationPathConfig;
use Yahvya\EagleFramework\Config\Config;
use Yahvya\EagleFramework\Config\ConfigException;
use Yahvya\EagleFramework\Config\DatabaseConfig;
use Yahvya\EagleFramework\Config\EnvConfig;
use Yahvya\EagleFramework\Config\FrameworkConfig;
use Yahvya\EagleFramework\Config\MaintenanceConfig;
use Yahvya\EagleFramework\Routing\Response\HtmlResponse;
use Yahvya\EagleFramework\Routing\Response\ResponseCode;
use Throwable;

/**
 * @brief Application manager
 */
abstract class Application
{
    /**
     * @var Config|null Application configuration
     */
    protected static ?Config $applicationConfig = null;

    /**
     * @brief Launch the application
     * @param Config $applicationConfig Application configuration
     * @@param bool $startRouting If the search and the rendering should be done
     * @return void
     */
    public static function launchApplication(Config $applicationConfig, bool $startRouting = true): void
    {
        self::$applicationConfig = $applicationConfig;

        try
        {
            self::requireNeededFiles();
            self::checkConfigs();

            require_once(
                APP_CONFIG->getConfig(name: "ROOT") .
                Application::getFrameworkConfig()->getConfig(name: FrameworkConfig::ROUTES_BASEDIR_PATH->value) .
                "/routes.php"
            );

            try
            {
                self::initDatabase();

                if ($startRouting)
                {
                    $routingManager = new RoutingManager();
                    $routingManager
                        ->start()
                        ->renderResponse();
                }
            } catch (Throwable $e)
            {
                if (
                    self::$applicationConfig
                        ->getConfig(name: ApplicationConfig::ENV_CONFIG->value)
                        ->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value)
                )
                    debugDie($e);
                else
                    throw $e;
            }
        } catch (Throwable)
        {
            self::showInternalErrorPage();
        }
    }

    /**
     * @return Config|null Application config or null if not defined
     */
    public static function getApplicationConfig(): ?Config
    {
        return self::$applicationConfig;
    }

    /**
     * @return Config Environment config
     * @throws ConfigException On error
     */
    public static function getEnvConfig(): Config
    {
        if (self::$applicationConfig === null) throw new ConfigException(message: "Environment configuration not found");

        return self::$applicationConfig->getConfig(ApplicationConfig::ENV_CONFIG->value);
    }

    /**
     * @return Config Framework configuration
     * @throws ConfigException On error
     */
    public static function getFrameworkConfig(): Config
    {
        if (self::$applicationConfig === null) throw new ConfigException(message: "Framework configuration not found");

        return self::$applicationConfig->getConfig(ApplicationConfig::FRAMEWORK_CONFIG->value);
    }

    /**
     * @brief Set env config
     * @param Config $envConfig New environment config
     * @return void
     * @throws ConfigException On error
     */
    public static function setEnvConfig(Config $envConfig): void
    {
        if (self::$applicationConfig === null)
            throw new ConfigException(message: "The application is not configured yet");

        self::$applicationConfig->setConfig(name: ApplicationConfig::ENV_CONFIG->value, value: $envConfig);
    }

    /**
     * @return Config Application default configuration
     */
    public static function getApplicationDefaultConfig(): Config
    {
        $appRoot = __DIR__ . "/../../..";

        return Config::create()
            ->setConfig(
                name: ApplicationPathConfig::ENV_CONFIG_FILEPATH->value,
                value: "$appRoot/configs/env.php"
            )
            ->setConfig(
                name: ApplicationPathConfig::FUNCTIONS_CONFIG_FILEPATH->value,
                value: "$appRoot/configs/functions.php"
            )
            ->setConfig(
                name: ApplicationPathConfig::FRAMEWORK_CONFIG_FILEPATH->value,
                value: "$appRoot/configs/framework.php"
            )
            ->setConfig(
                name: ApplicationPathConfig::BLADE_FUNCTIONS_CONFIG_FILEPATH->value,
                value: "$appRoot/configs/blade-config.php"
            )
            ->setConfig(
                name: ApplicationPathConfig::TWIG_FUNCTIONS_CONFIG_FILEPATH->value,
                value: "$appRoot/configs/twig-config.php"
            );
    }

    /**
     * @brief Include the required files
     * @return void
     * @throws ConfigException On error
     */
    protected static function requireNeededFiles(): void
    {
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
     * @brief Verify configurations
     * @return void
     * @throws ConfigException On error
     */
    protected static function checkConfigs(): void
    {
        if (self::$applicationConfig === null)
            throw new ConfigException(message: "Undefined application configuration");

        $envConfig = self::$applicationConfig->getConfig(name: ApplicationConfig::ENV_CONFIG->value);
        $envConfig->checkConfigs(...array_map(fn(EnvConfig $case): string => $case->value, EnvConfig::cases()));

        $frameworkConfig = self::$applicationConfig->getConfig(name: ApplicationConfig::FRAMEWORK_CONFIG->value);
        $frameworkConfig->checkConfigs(...array_map(fn(FrameworkConfig $case): string => $case->value, FrameworkConfig::cases()));

        $maintenanceConfig = $envConfig->getConfig(name: EnvConfig::MAINTENANCE_CONFIG->value);
        $maintenanceConfig->checkConfigs(...array_map(fn(MaintenanceConfig $case): string => $case->value, MaintenanceConfig::cases()));
    }

    /**
     * @brief Initialize the database if required
     * @return void
     * @throws ConfigException On error
     */
    protected static function initDatabase(): void
    {
        $databaseConfig = self::$applicationConfig
            ->getConfig(name: ApplicationConfig::ENV_CONFIG->value)
            ->getConfig(name: EnvConfig::DATABASE_CONFIG->value);

        if (!$databaseConfig->getConfig(name: DatabaseConfig::INIT_APP_WITH_CONNECTION->value)) return;

        $databaseConfig->checkConfigs(...array_map(fn(DatabaseConfig $case): string => $case->value, DatabaseConfig::cases()));

        $databaseConfig
            ->getConfig(name: DatabaseConfig::PROVIDER->value)
            ->initDatabase(providerConfig: $databaseConfig->getConfig(name: DatabaseConfig::PROVIDER_CONFIG->value));
    }

    /**
     * @brief Show the internal error page
     * @return void
     */
    protected static function showInternalErrorPage(): void
    {
        try
        {
            $response = new HtmlResponse(
                content: @file_get_contents(APP_CONFIG->getConfig("ROOT") . "/Src/views/default-pages/internal-error.html") ??
                "Erreur interne"
            );

            $response
                ->setResponseCode(code: ResponseCode::INTERNAL_SERVER_ERROR)
                ->renderResponse();
        } catch (Throwable)
        {
        }
    }
}