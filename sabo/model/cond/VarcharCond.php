<?php

namespace Sabo\Model\Cond;

use Attribute;
use Closure;

#[Attribute]
class VarcharCond implements Cond{
    /**
     * longueur maximum de la chaine
     */
    private int $maxLength;

    /**
     * longueur minimum de la chaine
     */
    private int $minLength;

    private string $errorMessage;

    public function __construct(int $minLength = 1,int $maxLength = 255,string $errorMessage = "Veuillez vérifier le contenu de la chaine saisie."){
        $this->maxLength = $maxLength;
        $this->minLength = $minLength;
        $this->errorMessage = $errorMessage;
    }

    public function checkCondWith(mixed $data):bool{
        if(gettype($data) == "string"){
            $len = strlen($data);

            return $len >= $this->minLength && $len <= $this->maxLength;
        }

        return false;
    }

    public function getErrorMessage():string{
        return $this->errorMessage;
    }

    public function getIsDisplayable():bool{
        return true;
    }
}