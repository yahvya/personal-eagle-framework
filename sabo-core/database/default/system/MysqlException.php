<?php

namespace SaboCore\Database\Default\System;

use Exception;

/**
 * @brief Exception mysql
 * @author yahaya bathily https://github.com/yahvya
 */
class MysqlException extends Exception{
    /**
     * @var bool Si le message peut être affiché
     */
    protected bool $isDisplayable;

    /**
     * @param string $message message d'erreur
     * @param bool $isDisplayable si le message peut être affiché
     */
    public function __construct(string $message,bool $isDisplayable = false){
        parent::__construct(message: $message);

        $this->isDisplayable = $isDisplayable;
    }

    /**
     * @return bool Si le message peut être affiché
     */
    public function getIsDisplayable(): bool{
        return $this->isDisplayable;
    }

    /**
     * @param string $defaultMessage message par défaut
     * @return string le message d'erreur s'il peut être affiché sinon le message par défaut
     */
    public function getErrorMessage(string $defaultMessage = "Une erreur technique s'est produite"):string{
        return $this->isDisplayable ? $this->message : $defaultMessage;
    }
}