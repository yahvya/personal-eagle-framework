<?php

namespace Yahvya\EagleFramework\Treatment;

use Exception;

/**
 * @brief Treatment exception
 */
class TreatmentException extends Exception
{
    /**
     * @param string $message Error message
     * @param bool $isDisplayable Whether the error is displayable to the user
     */
    public function __construct(
        string      $message,
        public bool $isDisplayable
    )
    {
        parent::__construct(message: $message);

        $this->message = $message;
    }

    /**
     * @param string $defaultMessage Default error message in case the message is not displayable
     * @return string The error message
     */
    public function getErrorMessage(string $defaultMessage = "An error occurred"): string
    {
        return $this->isDisplayable ? $this->message : $defaultMessage;
    }
}
