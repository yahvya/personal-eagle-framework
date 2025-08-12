<?php

namespace Yahvya\EagleFramework\Database\Default\Attributes;

/**
 * @brief SQL attribute abstract definition
 */
abstract class SqlAttribute
{
    /**
     * @return string Provide the SQL creation string of the attribute
     */
    public abstract function getCreationSql(): string;
}