<?php

namespace Sabo\Model\Cond;

use Attribute;
use DateTime;
use Exception;

/**
 * condition représentant un champs de type datetime
 */
#[Attribute]
class DatetimeCond implements Cond{
    /**
     * message d'erreur
     */
    private string $errorMessage;

    /**
     * @param errorMessage le message d'erreur affiché
     */
    public function __construct(string $errorMessage = "Une date au format correct est attendue"){
        $this->errorMessage = $errorMessage;
    }

    public function checkCondWith(mixed $data):bool{
        try{
            new DateTime($data);

            return true;
        }
        catch(Exception){}

        return false;
    }

    public function getIsDisplayable():bool{
        return true;
    }

    public function getErrorMessage():string{
        return $this->errorMessage;
    }
}