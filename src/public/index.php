<?php

use Sabo\Application\Context\Application\ApplicationContext;
use Sabo\Application\Context\DataContracts\PathConfigurationDto;
use Sabo\Application\Context\Hooks\SaboHooksDto;
use Sabo\Application\Launcher\Launcher\ApplicationLauncher;
use Sabo\Application\Launcher\Steps\EnvironmentConfigurationLoadingStep;
use Sabo\Application\Launcher\Steps\GlobalFunctionsLoadingStep;
use Sabo\Application\Launcher\Steps\RoutingStep;
use Sabo\Application\Launcher\Steps\SaboHookConfigurationLoadingStep;
use Sabo\Application\Launcher\Steps\UserDependenciesInjectorConfigurationStep;

# APPLICATION ENTRY POINT

$rootDirectoryPath = __DIR__ . "/../..";

# configure auto-loading
require_once "$rootDirectoryPath/vendor/autoload.php";

# define application default context
$applicationContext = new ApplicationContext(
    applicationPathConfiguration: new PathConfigurationDto(
        rootDirectoryPath: $rootDirectoryPath,
        configurationsDirectoryPath: "Src/configs"
    ),
    isInDevMode: true
);

$applicationLauncher = new ApplicationLauncher(
    new SaboHookConfigurationLoadingStep(),
    new UserDependenciesInjectorConfigurationStep(),
    new GlobalFunctionsLoadingStep(),
    new EnvironmentConfigurationLoadingStep(),
    new RoutingStep()
);

if(!$applicationLauncher->executeAll(executionContext: $applicationContext))
    die("An error occurred on the server");