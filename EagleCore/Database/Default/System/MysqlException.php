<?php

namespace Yahvya\EagleFramework\Database\Default\System;

use Exception;

/**
 * @brief Mysql exception
 */
class MysqlException extends Exception
{
    /**
     * @var bool Indicates if the message can be displayed
     */
    protected(set) bool $isDisplayable;

    /**
     * @param string $message Error message
     * @param bool $isDisplayable Indicates if the message can be displayed
     */
    public function __construct(string $message, bool $isDisplayable = false)
    {
        parent::__construct(message: $message);

        $this->isDisplayable = $isDisplayable;
    }

    /**
     * @param string $defaultMessage Default message
     * @return string The error message if it can be displayed, otherwise the default message
     */
    public function getErrorMessage(string $defaultMessage = "A technical error has occurred"): string
    {
        return $this->isDisplayable ? $this->message : $defaultMessage;
    }
}