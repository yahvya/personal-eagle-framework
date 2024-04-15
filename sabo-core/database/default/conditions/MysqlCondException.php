<?php

namespace SaboCore\Database\Default\Conditions;

use Exception;

/**
 * @brief Exception de non-validité de condition
 * @author yahaya bathily https://github.com/yahvya
 */
class MysqlCondException extends Exception{
    /**
     * @var Cond Condition invalide
     */
    protected Cond $failedCond;

    /**
     * @param Cond $failedCond Condition invalide
     */
    public function __construct(Cond $failedCond){
        parent::__construct($failedCond->getErrorMessage());

        $this->failedCond = $failedCond;
    }

    /**
     * @brief Fourni le message d'erreur formaté en fonction de l'état isDisplayable
     * @param string $defaultMessage message par défaut en cas de message non affichable
     * @return string Le message d'erreur affichable
     */
    public function getErrorMessage(string $defaultMessage = "Une erreur technique c'est produite"): string{
        return $this->failedCond->getIsDisplayable() ? $this->failedCond->getErrorMessage() : $defaultMessage;
    }

    /**
     * @return Cond L'action échouée
     */
    public function getFailedCond(): Cond{
        return $this->failedCond;
    }

    /**
     * @return bool Si le message peut être affiché à l'utilisateur
     */
    public function getIsDisplayable(): bool{
        return $this->failedCond->getIsDisplayable();
    }
}