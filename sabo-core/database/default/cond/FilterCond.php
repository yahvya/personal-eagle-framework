<?php

namespace SaboCore\Database\Default\Cond;

use Attribute;
use Override;

/**
 * @brief Condition filter_var
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class FilterCond implements Cond{
    /**
     * @brief Le message d'erreur
     */
    private string $errorMessage;

    /**
     * @brief Le filtre à valider
     */
    private int $filter;

    /**
     * @param int $filter constante FILTER_VALIDATE_...
     * @param string $errorMessage message d'erreur
     */
    public function __construct(int $filter,string $errorMessage){
        $this->filter = $filter;
        $this->errorMessage = $errorMessage;
    }

    #[Override]
    public function checkCondWith(mixed $data):bool{
        return filter_var($data,$this->filter);
    }

    #[Override]
    /**
     * @return bool si l'erreur peut être envoyée à l'utilisateur
     */
    public function getIsDisplayable():bool{
        return true;
    }

    #[Override]
    /**
     * @return string le message d'erreur en cas d'échec de validation de la condition
     */
    public function getErrorMessage():string{
        return $this->errorMessage;
    }   
}