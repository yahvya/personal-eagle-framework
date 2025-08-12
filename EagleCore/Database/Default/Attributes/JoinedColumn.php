<?php

namespace Yahvya\EagleFramework\Database\Default\Attributes;

use Attribute;

/**
 * @brief Join table attribute
 */
#[Attribute]
class JoinedColumn
{

    /**
     * @param string $classModel Model's class
     * @param array{string:string} $joinConfig "Join" configuration. Indexed by the attribute name of the current model associated with the attribute name of the joined model.
     * @param bool $loadOnGeneration If the data should be load on the model generation. If false, the method "loadContent" should be called on the JoinedList before its usage.
     */
    public function __construct(
        protected(set) string $classModel,
        protected(set) array $joinConfig,
        protected(set) bool $loadOnGeneration = true
    )
    {
    }
}