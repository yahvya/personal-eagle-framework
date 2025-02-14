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
    public function execute(StepExecutionContext &$executionContext): bool
    {
        $result = @require(PathBuilder::buildPath(
            ApplicationContext::$current?->applicationPathConfiguration->rootDirectoryPath,
            ApplicationContext::$current?->applicationPathConfiguration->configurationsDirectoryPath,
            "functions.php"
        ));

        return $result === true;
    }

    public function canGoToNextStep(): bool
    {
        return true;
    }
}