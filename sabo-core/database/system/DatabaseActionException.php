<?php

namespace SaboCore\Database\System;

use Exception;

/**
 * @brief Exception d'action sur un processus de la base de données
 * @author yahaya bathily https://github.com/yahvya
 */
class DatabaseActionException extends Exception{
    /**
     * @var string Message d'erreur
     */
    protected string $errorMessage;

    /**
     * @var DatabaseActions Action échouée
     */
    protected DatabaseActions $failedAction;

    /**
     * @var bool Si le message d'erreur peut être affiché
     */
    protected bool $isDisplayable;

    /**
     * @param string $errorMessage Message d'erreur
     * @param DatabaseActions $failedAction Action échouée
     * @param bool $isDisplayable Si le message d'erreur peut être affiché
     */
    public function __construct(string $errorMessage,DatabaseActions $failedAction,bool $isDisplayable = true){
        parent::__construct($errorMessage);

        $this->errorMessage = $errorMessage;
        $this->failedAction = $failedAction;
        $this->isDisplayable = $isDisplayable;
    }

    /**
     * @brief Fourni le message d'erreur formaté en fonction de l'état isDisplayable
     * @param string $defaultMessage message par défaut en cas de message non affichable
     * @return string Le message d'erreur affichable
     */
    public function getErrorMessage(string $defaultMessage = "Une erreur technique c'est produite"): string{
        return $this->isDisplayable ? $this->errorMessage : $defaultMessage;
    }

    /**
     * @return DatabaseActions L'action échouée
     */
    public function getFailedAction(): DatabaseActions{
        return $this->failedAction;
    }

    /**
     * @return bool Si le message peut être affiché à l'utilisateur
     */
    public function getIsDisplayable(): bool{
        return $this->isDisplayable;
    }
}