<?php

namespace Sabo\Application\Launcher\Steps;

use Sabo\Application\Context\Application\ApplicationContext;
use Sabo\Utils\Builders\PathBuilder;
use Sabo\Utils\StepsManager\Step;
use Sabo\Utils\StepsManager\StepExecutionContext;

/**
 * Environment file loading step
 */
class EnvironmentConfigurationLoadingStep extends Step
{
    public function execute(ApplicationContext|StepExecutionContext &$executionContext): bool
    {
        $result = @require(PathBuilder::buildPath(
            $executionContext->applicationPathConfiguration->rootDirectoryPath,
            $executionContext->applicationPathConfiguration->configurationsDirectoryPath,
            "env.php"
        ));

        if($result === true)
        {
            configureEnv(environment: $executionContext->environmentContext);
            $executionContext->hooks->onEnvironmentLoaded?->__invoke();
            return true;
        }

        return false;
    }

    public function canGoToNextStep(): bool
    {
        return true;
    }
}