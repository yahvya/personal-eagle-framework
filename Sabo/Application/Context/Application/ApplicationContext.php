<?php

namespace Sabo\Application\Context\Application;

use Sabo\Application\Context\DataContracts\PathConfigurationDto;
use Sabo\Application\Context\Hooks\SaboHooksDto;
use Sabo\Utils\DependencyInjector\DependencyInjectorManager;
use Sabo\Utils\Routes\RouteManager;
use Sabo\Utils\StepsManager\StepExecutionContext;

/**
 * Application context
 */
class ApplicationContext implements StepExecutionContext
{
    /**
     * @var ApplicationContext Application current context
     */
    public static ApplicationContext $current;

    /**
     * @var DependencyInjectorManager Linked dependency injector
     */
    public DependencyInjectorManager $dependencyInjector;

    /**
     * @var SaboHooksDto Hooks configuration
     */
    public SaboHooksDto $hooks;

    /**
     * @var EnvironmentContext Environment context
     */
    public EnvironmentContext $environmentContext;

    /**
     * @var RouteManager Route manager
     */
    public RouteManager $routeManager;

    /**
     * @param PathConfigurationDto $applicationPathConfiguration Application path configuration
     * @param bool $isInDevMode Application development state
     * @param bool $update If update the currant context of the application
     */
    public function __construct(
        public PathConfigurationDto $applicationPathConfiguration,
        public bool $isInDevMode,
        bool $update = true
    )
    {
        $this->dependencyInjector = $this->buildApplicationDefaultDependencyInjector();
        $this->hooks = new SaboHooksDto();
        $this->environmentContext = new EnvironmentContext();
        $this->routeManager = new RouteManager();

        if($update)
            static::$current = $this;
    }

    /**
     * @return DependencyInjectorManager Application dependency injector with default factories
     */
    public function buildApplicationDefaultDependencyInjector():DependencyInjectorManager
    {
        $dependencyInjectorManager = new DependencyInjectorManager();

        return $dependencyInjectorManager
            ->addDependencyFactory(classname: static::class,factory: fn():?ApplicationContext => $this)
            ->addDependencyFactory(classname: DependencyInjectorManager::class,factory: fn():DependencyInjectorManager => $dependencyInjectorManager)
            ->addDependencyFactory(classname: PathConfigurationDto::class,factory: fn():PathConfigurationDto => $this->applicationPathConfiguration)
            ->addDependencyFactory(classname: SaboHooksDto::class,factory: fn():SaboHooksDto => $this->hooks)
            ->addDependencyFactory(classname: EnvironmentContext::class,factory: fn():EnvironmentContext => $this->environmentContext)
            ->addDependencyFactory(classname: RouteManager::class,factory: fn():RouteManager => $this->routeManager);
    }
}