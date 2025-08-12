<?php

namespace Yahvya\EagleFramework\Database\System;

/**
 * @brief Query comparison marker
 */
class QueryComparator
{
    /**
     * @param string $comparator Comparator
     */
    protected function __construct(protected(set) string $comparator)
    {
    }
}
