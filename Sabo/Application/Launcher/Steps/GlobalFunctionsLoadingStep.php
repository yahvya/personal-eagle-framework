<?php

namespace Sabo\Application\Launcher\Steps;

use Sabo\Application\Context\Application\ApplicationContext;
use Sabo\Utils\Builders\PathBuilder;
use Sabo\Utils\StepsManager\Step;
use Sabo\Utils\StepsManager\StepExecutionContext;

/**
 * Global function loading step
 */
class GlobalFunctionsLoadingStep extends Step
{
    public function execute(ApplicationContext|StepExecutionContext &$executionContext): bool
    {
        $result = @require(PathBuilder::buildPath(
            $executionContext->applicationPathConfiguration->rootDirectoryPath,
            $executionContext->applicationPathConfiguration->configurationsDirectoryPath,
            "functions.php"
        ));

        if($result === true)
        {
            $executionContext->hooks->onGlobalFunctionsLoaded?->__invoke();
            return true;
        }

        return false;
    }

    public function canGoToNextStep(): bool
    {
        return true;
    }
}