<?php

namespace SaboCore\Core\Mappers\Annotations;

use Attribute;

/**
 * Dto Map attribute allow to give an alias to the dto attribute to match the mapping key
 */
#[Attribute]
readonly class DtoMap
{
    /**
     * @param string $alias Rename alias
     */
    public function __construct(
        public string $alias
    ){}
}