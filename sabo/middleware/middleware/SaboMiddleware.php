<?php

namespace Sabo\Middleware\Middleware;

use Sabo\Middleware\Exception\MiddlewareException;
use Sabo\Model\Exception\ModelCondException;

/**
 * class parent des middlewares
 */
abstract class SaboMiddleware{
    /**
     * @param toVerifyIn la tableau dans lequel vérifié ($_GET,$_POST,...)
     * @param keys les clés à vérifier 
     */
    protected static function checkIfNotEmptyIn(array $toVerifyIn,string ...$keys):bool{
        foreach($keys as $key){
            if(empty($toVerifyIn[$key]) ) return false;
        }

        return true;
    }

    /**
     * lève une exception à partir d'un message
     * @param errorMessage le message d'erreur
     * @param isDisplayable défini si l'erreur peut être affiché (par défaut true)
     * @throws MiddlewareException
     */
    protected static function throwException(string $errorMessage,bool $isDisplayable = true):void{
        throw new MiddlewareException($errorMessage,$isDisplayable);
    }

    /**
     * lève une exception à partir d'une condition échoué
     * @param condException la condition échouée
     * @throws MiddlewareException
     */
    protected static function throwModelCondException(ModelCondException $condException):void{
        $cond = $condException->getFailedCond();

        throw new MiddlewareException($cond->getErrorMessage(),$cond->getIsDisplayable() );    
    }
}