<?php

namespace Sabo\Application\Context;

/**
 * Application context
 */
class ApplicationContext
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