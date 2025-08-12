<?php

namespace Yahvya\EagleFramework\Database\System;

/**
 * @brief Query condition
 */
class QueryCondition
{
    /**
     * @param mixed $condGetter Condition getter
     * @param QueryComparator $comparator Condition comparatorComparateur
     * @param mixed $conditionValue Valeur à vérifier
     */
    public function __construct(
        protected(set) mixed $condGetter,
        protected(set) QueryComparator $comparator,
        protected(set) mixed $conditionValue
    )
    {
    }
}