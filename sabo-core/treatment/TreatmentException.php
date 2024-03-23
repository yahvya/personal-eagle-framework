<?php

namespace SaboCore\Treatment;

use Exception;

/**
 * @brief Exception de traitement
 * @author yahaya bathily https://github/yahvya.com
 */
class TreatmentException extends Exception{
    /**
     * @var bool si l'erreur est affichable à l'utilisateur
     */
    protected bool $isDisplayable;

    /**
     * @param string $message message d'erreur
     * @param bool $isDisplayable si l'erreur est affichable à l'utilisateur
     */
    public function __construct(string $message, bool $isDisplayable){
        parent::__construct(message: $message);

        $this->message = $message;
        $this->isDisplayable = $isDisplayable;
    }

    /**
     * @param string $defaultMessage message d'erreur par défaut en cas de message non affichable
     * @return string le message d'erreur
     */
    public function getErrorMessage(string $defaultMessage = "Une erreur s'est produite"):string{
        return $this->isDisplayable ? $this->message : $defaultMessage;
    }
}