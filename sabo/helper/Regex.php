<?php

namespace Sabo\Helper;

/**
 * constructeur de regex
 */
abstract class Regex{

    /**
     * @param minOne défini s'il faut au moins un chiffre
     * @return string la chaine regex attendant un entier
     */
    public static function intRegex(bool $minOne = true):string{
        return "[0-9]" . ($minOne ? "+" : "*");
    }

    /**
     * @param includeNumbers défini si les nombres doivent être inclus
     * @param minOne défini s'il faut au moins un caractère
     * @param adding élements à ajouter à la suite autorisé
     * @return string la chaine regex attendant une lettre de l'alphabet ou des chiffre
     */
    public static function strRegex(bool $includeNumbers = true,bool $minOne = true,string $adding = ""):string{
        return "[{$adding}a-zA-Z" . ($includeNumbers ? "0-9]" : "]") . ($minOne ? "+" : "*");
    }   
}