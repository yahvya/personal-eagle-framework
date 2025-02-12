<?php

use Sabo\Application\Context\ApplicationContext;
use Sabo\Application\Context\PathConfigurationDto;
use Sabo\Application\Launcher\Launcher\ApplicationLauncher;
use Sabo\Application\Launcher\Steps\EnvironmentConfigurationLoadingStep;
use Sabo\Application\Launcher\Steps\GlobalFunctionsLoadingStep;
use Sabo\Application\Launcher\Steps\RoutingStep;
use Sabo\Application\Launcher\Steps\SaboHookConfigurationLoadingStep;
use Sabo\Application\Launcher\Steps\UserDependenciesInjectorConfigurationStep;

$rootDirectoryPath = __DIR__ . "/../..";

# configure auto-loading
require_once "$rootDirectoryPath/vendor/autoload.php";

# define application default context
ApplicationContext::$current = new ApplicationContext(
    applicationPathConfiguration: new PathConfigurationDto(
        rootDirectoryPath: $rootDirectoryPath,
        configurationsDirectoryPath: "configs"
    )
);

$applicationLauncher = new ApplicationLauncher(
    new SaboHookConfigurationLoadingStep(),
    new UserDependenciesInjectorConfigurationStep(),
    new GlobalFunctionsLoadingStep(),
    new EnvironmentConfigurationLoadingStep(),
    new RoutingStep()
);

if(!$applicationLauncher->executeAll())
    die("An error occurred on the server");