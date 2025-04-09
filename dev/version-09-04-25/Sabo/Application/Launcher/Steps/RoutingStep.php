<?php

namespace Sabo\Application\Launcher\Steps;

use Sabo\Application\Context\Application\ApplicationContext;
use Sabo\Utils\Builders\PathBuilder;
use Sabo\Utils\StepsManager\Step;
use Sabo\Utils\StepsManager\StepExecutionContext;

/**
 * Routing step
 */
class RoutingStep extends Step
{
    public function execute(StepExecutionContext|ApplicationContext &$executionContext): bool
    {
        $result = @require(PathBuilder::buildPath(
            $executionContext->applicationPathConfiguration->rootDirectoryPath,
            $executionContext->applicationPathConfiguration->configurationsDirectoryPath,
            "routes.php",
        ));

        if($result !== true)
            return false;

        configureRoutes(routeManager: $executionContext->routeManager);

        return true;
    }

    public function canGoToNextStep(): bool
    {
        return true;
    }
}