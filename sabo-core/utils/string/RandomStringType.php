<?php

namespace SaboCore\Utils\String;

/**
 * liste les types de caractères utilisables
 */
enum RandomStringType:string{
    /**
     * caractères majuscules
     */
    case UPPERCHARS = "upperchars";
    /**
     * caractères minuscules
     */
    case LOWERCHARS = "lowerchars";
    /**
     * nombres
     */
    case NUMBERS = "numbers";
    /**
     * caractère spéciaux
     */
    case SPECIALCHARS = "specialchars";
}