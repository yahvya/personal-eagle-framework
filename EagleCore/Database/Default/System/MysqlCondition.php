<?php

namespace Yahvya\EagleFramework\Database\Default\System;

use Yahvya\EagleFramework\Database\System\QueryCondition;

/**
 * @brief Mysql condition
 */
class MysqlCondition extends QueryCondition
{
    /**
     * @param string|MysqlFunction $condGetter Attribute name or mysql function
     * @param MysqlComparator $comparator Comparator
     * @param mixed $conditionValue Value to check
     * @attention In the case of a getter as a condition don't provide an alias
     */
    public function __construct(mixed $condGetter, MysqlComparator $comparator, mixed $conditionValue)
    {
        parent::__construct($condGetter, $comparator, $conditionValue);
    }
}