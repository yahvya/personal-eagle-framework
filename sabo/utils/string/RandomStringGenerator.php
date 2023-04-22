<?php

namespace Sabo\Utils\String;

/**
 * générateur de chaine aléatoire
 */
trait RandomStringGenerator{
    /**
     * retourne une chaine aléatoire
     * @param length la taille de la chaine a généré (entier positif) par défaut 10
     * @param removeSimilarChars défini si les caractères pouvant se ressembler doivent se supprimer
     * @param toIgnore liste des types à ignorer lors de la construction de la chaine  
     * @return string la chaine généré
     */
    public static function generateString(int $length = 10,bool $removeSimilarChars = true,RandomStringType... $toIgnore):string{
        $chars = [
            RandomStringType::LOWERCHARS->value => "abcdefghjkmnpqrstuvwxyz",
            RandomStringType::UPPERCHARS->value => "ABCDEFGHJKMNPQRSTUVWXYZ",
            RandomStringType::NUMBERS->value => "123456789",
            RandomStringType::SPECIALCHARS->value => "&#{[(-_@)]}$%!"
        ];

        $similarChars = $removeSimilarChars ? [] : [
            RandomStringType::LOWERCHARS->value => "lio",
            RandomStringType::UPPERCHARS->value => "LIO",
            RandomStringType::NUMBERS->value => "0"
        ];

        // fusion des caractères similaires à la liste des caractères
        foreach($similarChars as $key => $charList) $chars[$key] = $chars[$key] . $charList;

        // suppression des caractères à ignorer
        foreach($toIgnore as $typeToIgnore) unset($chars[$typeToIgnore->value]);

        $choiceList = "";

        // création de la chaine de choix finale
        foreach($chars as $key => $charList) $choiceList .= $charList;

        $choiceList = str_split($choiceList);

        $keys = [];

        // récupération de l'index des caractères utilisés
        for($i = 0; $i < $length; $i++) array_push($keys,array_rand($choiceList) );

        if(gettype($keys) != "array") $keys = [$keys];

        $finalString = "";

        foreach($keys as $key) $finalString .= $choiceList[$key];

        return $finalString;
    }
}