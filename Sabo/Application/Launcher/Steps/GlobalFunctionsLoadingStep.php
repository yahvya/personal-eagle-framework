<?php

namespace Sabo\Application\Launcher\Steps;

use Sabo\Utils\StepsManager\Step;
use Sabo\Utils\StepsManager\StepExecutionContext;

/**
 * Global function loading step
 */
class GlobalFunctionsLoadingStep extends Step
{
    public function execute(StepExecutionContext &$executionContext): bool
    {
        return true;
    }

    public function canGoToNextStep(): bool
    {
        return true;
    }
}