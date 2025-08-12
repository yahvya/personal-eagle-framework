<?php

namespace Yahvya\EagleFramework\Treatment;

use Yahvya\EagleFramework\Database\Default\System\MysqlException;

/**
 * @brief Treatment manager
 */
abstract class Treatment
{
    /**
     * @brief Throws a treatment exception
     * @param string $errorMessage Error message
     * @param bool $isDisplayable Whether the message can be displayed
     * @return void
     * @throws TreatmentException The exception
     */
    protected static function throwException(string $errorMessage, bool $isDisplayable = true): void
    {
        throw new TreatmentException(message: $errorMessage, isDisplayable: $isDisplayable);
    }

    /**
     * @brief Throws a treatment exception
     * @param MysqlException $exception The condition exception
     * @return void
     * @throws TreatmentException The exception
     */
    protected static function throwModelException(MysqlException $exception): void
    {
        throw new TreatmentException(message: $exception->getMessage(), isDisplayable: $exception->isDisplayable);
    }
}
