<?php

namespace Sabo\Model\Cond;

use Attribute;

/**
 * attribut définissant une condition regex
 */
#[Attribute]
class RegexCond implements Cond{
    private string $errorMessage;
    private string $regex;
    private string $regexOptions;
    private string $delimitor;

    /**
     * @param string $regex la chaine regex
     * @param string $errorMessage le message d'erreur en cas de non validation
     * @param string $regexOptions les options à ajouter sur la regex
     * @param string $delimitor le délimiteur de la regex (1 caractère)  
     */
    public function __construct(string $regex,string $errorMessage,string $regexOptions = "",string $delimitor = "#"){
        $this->regex = $regex;
        $this->errorMessage = $errorMessage;
        $this->regexOptions = $regexOptions;
        $this->delimitor = strlen($delimitor) == 1 ? $delimitor : "#";
    }

    public function checkCondWith(mixed $data):bool{
        return @preg_match("{$this->delimitor}{$this->regex}{$this->delimitor}{$this->regexOptions}",$data);
    }

    public function getIsDisplayable():bool{
        return true;
    }

    public function getErrorMessage():string{
        return $this->errorMessage;
    }
}