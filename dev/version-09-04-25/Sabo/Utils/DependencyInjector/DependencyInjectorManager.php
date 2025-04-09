<?php

namespace Sabo\Utils\DependencyInjector;

use Closure;
use ReflectionFunction;
use Throwable;
use TypeError;

/**
 * Dependency injector manager
 */
class DependencyInjectorManager
{
    /**
     * @var array{string:Closure} Dependencies factories
     */
    protected array $dependenciesFactories = [];

    /**
     * @var array{string:Closure} Dependencies instances
     */
    protected array $alreadyBuiltDependencies = [];

    /**
     * Add a dependency factory
     * @param string $classname Element type class
     * @param Closure $factory Element builder
     * @return $this
     */
    public function addDependencyFactory(string $classname,Closure $factory):static
    {
        $this->dependenciesFactories[$classname] = $factory;

        return $this;
    }

    /**
     * Verify if this manager contain the factory of the provided class
     * @param string $class Class
     * @return bool If contain
     */
    public function containFactoryOf(string $class):bool
    {
        return @array_key_exists(key: $class,array: $this->dependenciesFactories);
    }

    /**
     * Build the instance of the searched dependency
     * @param string $class Class
     * @param bool $uniqueInstance If true, it will provide the same instance each time except for the first call
     * @param mixed|null $factoryContext Context to provide to the factory method
     * @attention The factory closure must not throw an exception. It won't be 'catch' by this method
     * @attention Providing the same class two times will override the first call
     * @return object Null on failure or the built instance
     */
    public function getDependencyInstance(string $class,bool $uniqueInstance = true,mixed $factoryContext = null):mixed
    {
        if(!$this->containFactoryOf(class: $class))
            return null;

        # use an already built element
        if($uniqueInstance && array_key_exists(key: $class,array: $this->alreadyBuiltDependencies))
            return $this->alreadyBuiltDependencies[$class];

        # build a new element
        $factoryResult = $factoryContext !== null ?
            $this->dependenciesFactories[$class]->__invoke($factoryContext) :
            $this->dependenciesFactories[$class]->__invoke();

        if($factoryResult === null)
            return null;

        # register the element
        $this->alreadyBuiltDependencies[$class] = $factoryResult;

        return $factoryResult;
    }

    /**
     * Build the callable params based on the registered factories
     * @param Closure|array $callable Callable
     * @param array{string:mixed} $factoriesContext Factories context indexed by the ::class
     * @param bool $uniqueInstance Use unique instance of elements
     * @return array{string:array}|null ["params" => ,"missing" => ] or null on failure
     */
    public function buildCallableParams(Closure|array $callable,array $factoriesContext = [], bool $uniqueInstance = true):?array
    {
        # transform array callable to closure
        if(gettype($factoriesContext) === "array")
            $callable = $callable(...);

        try
        {
            $reflectionFunction = new ReflectionFunction(function: $callable);
            $foundedParams = [];
            $missingParams = [];

            foreach($reflectionFunction->getParameters() as $parameter)
            {
                # retrieve parameter name and try to load an instance
                $parameterClassname = $parameter->getType()->getName();
                $parameterName = $parameter->getName();
                $parameterInstance = $this->getDependencyInstance(
                    class: $parameterClassname,
                    uniqueInstance: $uniqueInstance,
                    factoryContext: $factoriesContext[$parameterClassname] ?? null
                );

                # parameter type factory not found
                if($parameterInstance === null)
                {
                    $missingParams[] = $parameterName;
                    continue;
                }

                $foundedParams[$parameterName] = $parameterInstance;
            }

            return [
                "params" => $foundedParams,
                "missing" => $missingParams
            ];
        }
        catch(Throwable|TypeError)
        {
            return null;
        }
    }
}