<?php

namespace SaboCore\Database\Default\Cond;

use Attribute;
use Override;

/**
 * @brief Condition + marquage représentant un champ de type varchar
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class VarcharCond implements Cond{
    /**
     * @brief Longueur maximale de la chaine
     */
    private int $maxLength;

    /**
     * @brief Longueur minimum de la chaine
     */
    private int $minLength;

    /**
     * @var string Message d'erreur
     */
    private string $errorMessage;

    /**
     * @param int $minLength la taille minimum de la chaine contenue (par défaut 1)
     * @param int $maxLength la taille maximum de la chaine contenue (par défaut 2)
     * @param string $errorMessage le message à afficher en cas de non validation
     */
    public function __construct(int $minLength = 1,int $maxLength = 255,string $errorMessage = "Veuillez vérifier le contenu de la chaine saisie."){
        $this->maxLength = $maxLength;
        $this->minLength = $minLength;
        $this->errorMessage = $errorMessage;
    }

    #[Override]
    public function checkCondWith(mixed $data):bool{
        if(gettype($data) == "string"){
            $len = strlen($data);

            return $len >= $this->minLength && $len <= $this->maxLength;
        }

        return false;
    }

    #[Override]
    public function getErrorMessage():string{
        return $this->errorMessage;
    }

    #[Override]
    public function getIsDisplayable():bool{
        return true;
    }
}