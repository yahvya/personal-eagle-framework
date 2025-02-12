<?php

namespace Sabo\Application\Context;

use Sabo\Utils\StepsManager\StepExecutionContext;

/**
 * Application context
 */
class ApplicationContext implements StepExecutionContext
{
    /**
     * @var ApplicationContext|null Application current context
     */
    public static ?ApplicationContext $current = null;

    /**
     * @param PathConfigurationDto $applicationPathConfiguration Application path configuration
     */
    public function __construct(
        public PathConfigurationDto $applicationPathConfiguration
    ){}
}