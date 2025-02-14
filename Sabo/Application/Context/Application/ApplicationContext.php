<?php

namespace Sabo\Application\Context\Application;

use Sabo\Application\Context\DataContracts\PathConfigurationDto;
use Sabo\Application\Context\Hooks\SaboHooksDto;
use Sabo\Utils\DependencyInjector\DependencyInjectorManager;
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
     * @param SaboHooksDto $hooks Hooks configuration
     * @param bool $isInDevMode Application development state
     * @param DependencyInjectorManager $dependencyInjectorManager Dependency injector manager
     */
    public function __construct(
        public PathConfigurationDto $applicationPathConfiguration,
        public SaboHooksDto $hooks,
        public bool $isInDevMode,
        public DependencyInjectorManager $dependencyInjectorManager
    ){}

    /**
     * @return DependencyInjectorManager Application dependency injector with default factories
     */
    public static function buildApplicationDefaultDependencyInjector():DependencyInjectorManager
    {
        return new DependencyInjectorManager()
            ->addDependencyFactory(classname: static::class,factory: fn():?ApplicationContext => static::$current);
    }
}