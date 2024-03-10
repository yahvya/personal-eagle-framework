<?php

namespace SaboCore\Database\Default\Cond;

use Attribute;
use DateTime;
use Exception;
use Override;

/**
 * @brief Condition représentant un champ de type datetime
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class DatetimeCond implements Cond{
    /**
     * @brief Message d'erreur
     */
    private string $errorMessage;

    /**
     * @param string $errorMessage le message d'erreur affiché
     */
    public function __construct(string $errorMessage = "Une date au format correct est attendue"){
        $this->errorMessage = $errorMessage;
    }

    #[Override]
    public function checkCondWith(mixed $data):bool{
        try{
            new DateTime($data);

            return true;
        }
        catch(Exception){}

        return false;
    }

    #[Override]
    public function getIsDisplayable():bool{
        return true;
    }

    #[Override]
    public function getErrorMessage():string{
        return $this->errorMessage;
    }
}