<?php

namespace Yahvya\EagleFramework\Database\System;

/**
 * @brief Query conditions separator
 */
class QueryCondSeparator
{
    /**
     * @param string $separator Separator
     */
    protected function __construct(protected(set) string $separator)
    {
    }
}
