<?php

namespace Sabo\Application\Launcher\Steps;

use Sabo\Application\Context\Application\ApplicationContext;
use Sabo\Utils\Builders\PathBuilder;
use Sabo\Utils\StepsManager\Step;
use Sabo\Utils\StepsManager\StepExecutionContext;

/**
 * User dependency injector  step
 */
class UserDependenciesInjectorConfigurationStep extends Step
{
    public function execute(StepExecutionContext &$executionContext): bool
    {
        $result = @require(PathBuilder::buildPath(
            ApplicationContext::$current->applicationPathConfiguration->rootDirectoryPath,
            ApplicationContext::$current->applicationPathConfiguration->configurationsDirectoryPath,
            "dependency-injector.php",
        ));

        if($result === true)
        {
            ApplicationContext::$current?->hooks?->onDependencyInjectorLoaded?->__invoke();
            return true;
        }

        return false;
    }

    public function canGoToNextStep(): bool
    {
        return true;
    }
}