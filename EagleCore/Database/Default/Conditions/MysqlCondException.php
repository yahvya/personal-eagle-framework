<?php

namespace Yahvya\EagleFramework\Database\Default\Conditions;

use Exception;

/**
 * @brief Invalid condition exception
 */
class MysqlCondException extends Exception
{
    /**
     * @param Cond $failedCond Failed condition
     */
    public function __construct(protected(set) Cond $failedCond)
    {
        parent::__construct($failedCond->errorMessage);
    }

    /**
     * @brief Provided the formated error message based on the displayable state of the error
     * @param string $defaultMessage Default error message
     * @return string Formated error message
     */
    public function getErrorMessage(string $defaultMessage = "A technical error has occurred"): string
    {
        return $this->failedCond ? $this->failedCond->errorMessage : $defaultMessage;
    }

    public bool $isDisplayable {
        get => $this->failedCond->isDisplayable;
    }
}