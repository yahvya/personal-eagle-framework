<?php

namespace SaboCore\Database\Default\Cond;

use Attribute;
use DateTime;
use Override;
use Throwable;

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
            new DateTime(datetime: $data);

            return true;
        }
        catch(Throwable){}

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