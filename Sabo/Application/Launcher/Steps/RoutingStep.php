<?php

namespace Sabo\Application\Launcher\Steps;

use Sabo\Utils\StepsManager\Step;
use Sabo\Utils\StepsManager\StepExecutionContext;

/**
 * Routing step
 */
class RoutingStep extends Step
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