<?php

namespace SaboCore\Database\Default\Conditions;

use Attribute;
use Override;
use SaboCore\Database\Default\System\MysqlModel;

/**
 * @brief Condition de vérification de taille
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class LenCond implements Cond{
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
    public function verifyData(MysqlModel $baseModel,string $attributeName,mixed $data):bool{
        if(gettype(value: $data) == "string"){
            $len = strlen(string: $data);

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