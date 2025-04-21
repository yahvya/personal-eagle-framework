<?php

namespace SaboCore\Core\Mappers\Implementation;

use Exception;
use ReflectionClass;
use SaboCore\Core\Definitions\Mapper;
use SaboCore\Core\Mappers\Annotations\DtoMap;
use TypeError;

/**
 * Data contract mapper from array
 */
class ArrayDtoMapper implements Mapper
{
    /**
     * Map the array data in the container
     * @param array{string,mixed} $data Data to map
     * @param string $in Dto class
     * @return mixed Dto instance
     * @attention The array keys will be used to match the dto attribute if no rename annotation is present
     * @attention An attribute can have multiple dto map annotation , they will be tested in order in one key match
     */
    public function map(mixed $data, mixed $in): mixed
    {
        try
        {
            if(!class_exists(class: $in))
                return null;

            $dtoClassReflection = new ReflectionClass(objectOrClass: $in);

            // build the data contract
            $dtoInstance = $dtoClassReflection->newInstance();

            foreach($dtoClassReflection->getProperties() as $property)
            {
                $propertyLinkedKey = $property->getName();
                $mappingAttributes = $property->getAttributes(name: DtoMap::class);

                foreach($mappingAttributes as $mappingAttribute)
                {
                    $mappingAttributeInstance = $mappingAttribute->newInstance();

                    if(array_key_exists(key: $mappingAttributeInstance->alias,array: $data))
                    {
                        $propertyLinkedKey = $mappingAttributeInstance->alias;
                        break;
                    }
                }

                $property->setValue(objectOrValue: $dtoInstance,value: $data[$propertyLinkedKey] ?? null);
            }

            return $dtoInstance;
        }
        catch(TypeError|Exception)
        {
            return null;
        }
    }
}