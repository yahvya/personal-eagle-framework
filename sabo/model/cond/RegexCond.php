<?php

namespace Sabo\Model\Cond;

use Attribute;

#[Attribute]
class RegexCond implements Cond{
    private string $errorMessage;
    private string $regex;
    private string $regexOptions;
    private string $separator;

    public function __construct(string $regex,string $errorMessage,string $regexOptions = "",string $separator = "#"){
        $this->regex = $regex;
        $this->errorMessage = $errorMessage;
        $this->regexOptions = $regexOptions;
        $this->separator = strlen($separator) == 1 ? $separator : "#";
    }

    public function checkCondWith(mixed $data):bool{
        return @preg_match("{$this->separator}{$this->regex}{$this->separator}{$this->regexOptions}",$data);
    }

    public function getIsDisplayable():bool{
        return true;
    }

    public function getErrorMessage():string{
        return $this->errorMessage;
    }
}