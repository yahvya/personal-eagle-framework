<?php

namespace Sabo\Model\Cond;

use Attribute;

/**
 * condition représentant un champs de type varchar
 */
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

    /**
     * @param minLength la taille minimum de la chaine contenue (par défaut 1)
     * @param maxLength la taille maximum de la chaine contenue (par défaut 2)
     * @param errorMessage le message à afficher en cas de non validation
     */
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