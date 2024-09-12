<?php

namespace SaboCore\Utils\Injection\Injector;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use SaboCore\Application\Application\ApplicationState;
use SaboCore\Utils\CustomTypes\Map;

/**
 * @brief dependency injector
 */
readonly class DependencyInjector{
    /**
     * @param Map $factories types factories, keys are the classes and values are the linked factories
     */
    public function __construct(public Map $factories = new Map()){
    }

    /**
     * @brief create an instance of the given class
     * @param string $class class
     * @return mixed the class or null on fail
     * @throws Exception on error
     */
    public function createFromClass(string $class,array $baseElements = []):mixed{
        if(!$this->factories->haveKey(key: $class)){
            if(!is_object(value: $class))
                return null;

            # check if class have an empty params constructor
            $reflection = new ReflectionClass(objectOrClass: $class);

            if(count(value: $reflection->getConstructor()->getParameters()) === 0)
                return $reflection->newInstance();

            return null;
        }

        return call_user_func_array(callback: $this->factories->get(key: $class),args: [$baseElements]);
    }

    /**
     * @brief build an ordered array representing the callable args
     * @param Closure|array $callable callable
     * @param array $baseElements default values for potentiel parameters
     * @return array callable args
     * @throws Exception on error
     */
    public static function buildCallableArgs(Closure|array $callable,array $baseElements = []):array{
        $args = [];

        $argsReflections = static::getCallableArgsReflections(callable: $callable);

        foreach($argsReflections as $argReflection){
            # load from base elements

            if(array_key_exists(key: $argReflection->name,array: $baseElements)){
                $args[] = $baseElements[$argReflection->name];
                continue;
            }

            # load from type
            $type = $argReflection->getType();

            if($type === null)
                throw new Exception(message: "fail to access the parameter <$argReflection->name> type");

            $typeName = $type->getName();
            $instance = ApplicationState::$injector->createFromClass(class: $typeName,baseElements: $baseElements);

            if($instance === null)
                throw new Exception(message: "fail to create an instance of <$typeName> for <$argReflection->name> param");

            $args[] = $instance;
        }

        return $args;
    }

    /**
     * @brief read the callable arguments reflections
     * @param Closure|array $callable callable
     * @return ReflectionParameter[] list of reflections
     * @throws Exception on error
     */
    public static function getCallableArgsReflections(Closure|array $callable):array{
        if($callable instanceof Closure){
            $closureReflection = new ReflectionFunction(function: $callable);

            return $closureReflection->getParameters();
        }
        else{
            $methodReflection = new ReflectionMethod(objectOrMethod: $callable[0],method: $callable[1]);

            return $methodReflection->getParameters();
        }
    }
}