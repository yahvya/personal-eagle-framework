<?php

// FRAMEWORK UTILS

use SaboCore\Core\Mappers\Implementation\ArrayDtoMapper;

/**
 * @return ArrayDtoMapper A singleton array dto mapper
 */
function arrayDtoMapper():ArrayDtoMapper{
    static $arrayDtoMapper = new ArrayDtoMapper();

    return $arrayDtoMapper;
}