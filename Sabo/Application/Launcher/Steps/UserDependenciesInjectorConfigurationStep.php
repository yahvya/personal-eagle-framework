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
    public function execute(ApplicationContext|StepExecutionContext &$executionContext): bool
    {
        $result = @require(PathBuilder::buildPath(
            $executionContext->applicationPathConfiguration->rootDirectoryPath,
            $executionContext->applicationPathConfiguration->configurationsDirectoryPath,
            "dependency-injector.php",
        ));

        if($result === true)
        {
            configureDependencyInjector(dependencyInjector: $executionContext->dependencyInjector);
            $executionContext->hooks->onDependencyInjectorLoaded?->__invoke();
            return true;
        }

        return false;
    }

    public function canGoToNextStep(): bool
    {
        return true;
    }
}