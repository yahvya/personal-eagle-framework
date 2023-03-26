<?php

namespace Sabo\Middleware\Exception;

use Exception;

/**
 * exception provenant d'un middleware
 */
class MiddlewareException extends Exception{
    /**
     * défini si l'exception peut être affiché
     */
    private bool $isDisplayable;

    /**
     * @param errorMessage le message d'erreur de l'exception
     * @param isDispblayable défini si l'exception peut être affiché ou non à l'utilisateur
     */
    public function __construct(string $errorMessage,bool $isDisplayable = true){
        parent::__construct($errorMessage);
        
        $this->isDisplayable = $isDisplayable;
    }

    /**
     * @return bool si l'exception peut être affiché à l'utilisateur
     */
    public function getIsDisplayable():bool{
        return $this->isDisplayable;
    }
}