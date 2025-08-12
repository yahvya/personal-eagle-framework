<?php

namespace Yahvya\EagleFramework\Database\System;

use Exception;

/**
 * @brief Database action exception
 */
class DatabaseActionException extends Exception
{
    /**
     * @param string $errorMessage Error message
     * @param DatabaseActions $failedAction Failed action enum
     * @param bool $isDisplayable If the error message can be displayed to the user
     */
    public function __construct(
        protected(set) string $errorMessage,
        protected(set) DatabaseActions $failedAction,
        protected(set) bool $isDisplayable = true
    )
    {
        parent::__construct($errorMessage);
    }

    /**
     * @brief Provide the formated error message based on the 'isDisplayable' state
     * @param string $defaultMessage Default message in case of a non-displayable message
     * @return string Formated error message
     */
    public function getErrorMessage(string $defaultMessage = "An error occurred"): string
    {
        return $this->isDisplayable ? $this->errorMessage : $defaultMessage;
    }
}