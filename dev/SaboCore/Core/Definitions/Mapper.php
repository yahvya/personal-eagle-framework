<?php

namespace SaboCore\Core\Definitions;

/**
 * Mapper
 */
interface Mapper
{
    /**
     * Map the data in the provided element
     * @param mixed $data Data to map
     * @param mixed $in Map container
     * @return mixed Map container filled with the data
     */
    public function map(mixed $data,mixed $in):mixed;
}