<?php

namespace Sabo\Model\Cond;

use Attribute;

/**
 * condition filter_var
 */
#[Attribute]
class FilterCond implements Cond{
    /**
     * le message d'erreur
     */
    private string $errorMessage;

    /**
     * le filtre à valider
     */
    private int $filter;

    public function __construct(int $filter,string $errorMessage){
        $this->filter = $filter;
        $this->errorMessage = $errorMessage;
    }

    public function checkCondWith(mixed $data):bool{
        return filter_var($data,$this->filter);
    }

    /**
     * @return bool si l'erreur peut être envoyé à l'utilisateur
     */
    public function getIsDisplayable():bool{
        return true;
    }

    /**
     * @return string le message d'erreur en cas d'échec de validation de la condition
     */
    public function getErrorMessage():string{
        return $this->errorMessage;
    }   
}