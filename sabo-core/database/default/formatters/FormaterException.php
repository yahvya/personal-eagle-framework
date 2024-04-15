<?php

namespace SaboCore\Database\Default\Formatters;

use Exception;

/**
 * @brief Exception d'échec de formatage
 * @author yahaya bathily https://github.com/yahvya
 */
class FormaterException extends Exception{
    /**
     * @var Formater Formateur échoué
     */
    protected Formater $failedFormater;

    /**
     * @var bool Si le message peut être affiché
     */
    protected bool $isDisplayable;

    /**
     * @param Formater $failedFormater Formateur échoué
     */
    public function __construct(Formater $failedFormater,string $errorMessage,bool $isDisplayable = true){
        parent::__construct($errorMessage);

        $this->failedFormater = $failedFormater;
        $this->isDisplayable = $isDisplayable;
    }

    /**
     * @brief Fourni le message d'erreur formaté en fonction de l'état isDisplayable
     * @param string $defaultMessage message par défaut en cas de message non affichable
     * @return string Le message d'erreur affichable
     */
    public function getErrorMessage(string $defaultMessage = "Une erreur technique c'est produite"): string{
        return $this->isDisplayable ? $this->message : $defaultMessage;
    }

    /**
     * @return Formater Le formateur échoué
     */
    public function getFailedFormater(): Formater{
        return $this->failedFormater;
    }

    /**
     * @return bool Si le message peut être affiché à l'utilisateur
     */
    public function getIsDisplayable(): bool{
        return $this->isDisplayable;
    }
}