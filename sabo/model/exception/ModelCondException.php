<?php

namespace Sabo\Model\Exception;

use Exception;
use Sabo\Model\Cond\Cond;

/**
 * exception en cas d'échec d'assignation sur condition
 */
class ModelCondException extends Exception{
    /**
     * la condition échouée
     */
    private Cond $failedCond;

    /**
     * @param failedCond la condition qui n'a pas été valide
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