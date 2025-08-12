<?php

namespace Yahvya\EagleFramework\Database\Default\System;

use Yahvya\EagleFramework\Database\System\QueryCondSeparator;

/**
 * @brief Mysql separators
 */
class MysqlCondSeparator extends QueryCondSeparator
{
    /**
     * @return MysqlCondSeparator AND separator
     */
    public static function AND(): MysqlCondSeparator
    {
        return new MysqlCondSeparator(separator: "AND");
    }

    /**
     * @return MysqlCondSeparator OR separator
     */
    public static function OR(): MysqlCondSeparator
    {
        return new MysqlCondSeparator(separator: "OR");
    }

    /**
     * @return MysqlCondSeparator NOT separator
     */
    public static function NOT(): MysqlCondSeparator
    {
        return new MysqlCondSeparator(separator: "NOT");
    }

    /**
     * @return MysqlCondSeparator IS NULL separator
     */
    public static function IS_NULL(): MysqlCondSeparator
    {
        return new MysqlCondSeparator(separator: "IS NULL");
    }

    /**
     * @return MysqlCondSeparator IS NOT NULL separator
     */
    public static function IS_NOT_NULL(): MysqlCondSeparator
    {
        return new MysqlCondSeparator(separator: "IS NOT NULL");
    }

    /**
     * @return MysqlCondSeparator Start a condition froup
     */
    public static function GROUP_START(): MysqlCondSeparator
    {
        return new MysqlCondSeparator(separator: "(");
    }

    /**
     * @return MysqlCondSeparator Ends a condition froup
     */
    public static function GROUP_END(): MysqlCondSeparator
    {
        return new MysqlCondSeparator(separator: ")");
    }
}