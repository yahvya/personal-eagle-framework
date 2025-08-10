<?php

namespace Yahvya\EagleFramework\Utils\String;

/**
 * @brief List of available characters types
 */
enum RandomStringType: string
{
    /**
     * @brief Uppercase chars
     */
    case UPPER_CHARS = "upper_chars";

    /**
     * @brief Lowercase chars
     */
    case LOWER_CHARS = "lower_chars";

    /**
     * @brief Numbers
     */
    case NUMBERS = "numbers";

    /**
     * @brief Special chars
     */
    case SPECIAL_CHARS = "special_chars";
}