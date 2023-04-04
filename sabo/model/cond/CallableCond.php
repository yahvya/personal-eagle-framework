<?php

namespace Sabo\Model\Cond;

use Attribute;
use Closure;

/**
 * représente une condition appellable
 */
#[Attribute]
class CallableCond implements Cond{
    /**
     * le callable booléen à vérifier
     */
    private array|Closure $toVerify;

    /**
     * message d'erreur
     */
    private string $errorMessage;

    /**
     * si l'erreur peut être affiché
     */
    private bool $isDisplayable;
    
    /**
     * @param toVerify le callable à vérifier, doit renvoyer un booléen
     * @param errorMessage le message d'erreur
     * @param isDisplayable défini si l'erreur peut être affiché
     */
    public function __construct(callable $toVerify,string $errorMessage,bool $isDisplayable){
        $this->toVerify = $toVerify;
        $this->errorMessage = $errorMessage;
        $this->isDisplayable = $isDisplayable;
    }

    public function checkCondWith(mixed $data):bool{
        return call_user_func($this->toVerify,$data);
    }

    public function getIsDisplayable():bool{
        return $this->isDisplayable;
    }

    public function getErrorMessage():string{
        return $this->errorMessage;
    }
}
