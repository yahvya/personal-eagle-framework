<?php

namespace SaboCore\Database\Default\Exception;

use Exception;
use SaboCore\Database\Default\Cond\Cond;

/**
 * @brief Exception en cas d'échec d'assignation sur condition
 * @author yahaya bathily https://github.com/yahvya
 */
class ModelCondException extends Exception{
    /**
     * @brief La condition échouée
     */
    private Cond $failedCond;

    /**
     * @param Cond $failedCond La condition qui n'a pas été valide
     */
    public function __construct(Cond $failedCond){
        parent::__construct($failedCond->getErrorMessage() );

        $this->failedCond = $failedCond;
    }

    /**
     * @return Cond la condition échouée
     */
    public function getFailedCond():Cond{
        return $this->failedCond;
    }

    /**
     * @return bool si l'erreur de la condition peut être affiché
     */
    public function getIsDisplayable():bool{
        return $this->failedCond->getIsDisplayable();
    }
}