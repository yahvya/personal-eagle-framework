<?php

namespace Sabo\Application\Launcher\Steps;

use Sabo\Utils\StepsManager\Step;
use Sabo\Utils\StepsManager\StepExecutionContext;

/**
 * User dependency injector  step
 */
class UserDependenciesInjectorConfigurationStep extends Step
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