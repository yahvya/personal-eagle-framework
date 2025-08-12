<?php

namespace Yahvya\EagleFramework\Database\Default\Formater;

use Exception;

/**
 * @brief Formating failure's exception
 */
class FormaterException extends Exception
{
    /**
     * @param Formater $failedFormater Failed formater
     * @param string $errorMessage Error message
     * @param bool $isDisplayable If the message can be displayed to the user of the app
     */
    public function __construct(
        protected(set) Formater $failedFormater,
        string   $errorMessage,
        protected(set) bool $isDisplayable = true)
    {
        parent::__construct($errorMessage);
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