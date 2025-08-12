<?php

namespace Yahvya\EagleFramework\Utils\String;

/**
 * @brief Random string generator
 */
abstract class RandomStringGenerator
{
    /**
     * @brief Build a random string
     * @param int $length Expected string length (> 1)
     * @param bool $removeSimilarChars If true, the similar characters like (i,l) will be removed from the possibilities
     * @param RandomStringType ...$toIgnore Character types to ignore during the string build
     * @return string la chaine générée
     */
    public static function generateString(int $length = 10, bool $removeSimilarChars = true, RandomStringType ...$toIgnore): string
    {
        $chars = [
            RandomStringType::LOWER_CHARS->value => "abcdefghjkmnpqrstuvwxyz",
            RandomStringType::UPPER_CHARS->value => "ABCDEFGHJKMNPQRSTUVWXYZ",
            RandomStringType::NUMBERS->value => "123456789",
            RandomStringType::SPECIAL_CHARS->value => "&#{[(-_@)]}$%!"
        ];

        $similarChars = $removeSimilarChars ? [] : [
            RandomStringType::LOWER_CHARS->value => "lio",
            RandomStringType::UPPER_CHARS->value => "LIO",
            RandomStringType::NUMBERS->value => "0"
        ];

        foreach ($similarChars as $key => $charList) $chars[$key] = $chars[$key] . $charList;

        foreach ($toIgnore as $typeToIgnore) unset($chars[$typeToIgnore->value]);

        $choiceList = implode(separator: "", array: $chars);
        $choiceList = str_split(string: $choiceList);

        $keys = [];

        for ($i = 0; $i < $length; $i++)
            $keys[] = array_rand(array: $choiceList);

        if (gettype(value: $keys) != "array")
            $keys = [$keys];

        $finalString = "";

        foreach ($keys as $key)
            $finalString .= $choiceList[$key];

        return $finalString;
    }
}