<?php

namespace Yahvya\EagleFramework\Utils\Printer;

use BeBat\ConsoleColor\Style;
use BeBat\ConsoleColor\Style\Color;
use BeBat\ConsoleColor\Style\BackgroundColor;
use BeBat\ConsoleColor\Style\Composite;
use BeBat\ConsoleColor\Style\Text;

/**
 * @brief CLI printer util
 */
abstract class Printer
{
    /**
     * @brief Print the provided text without any changes
     * @param string $toPrint Text to print
     * @param Color $textColor Text color
     * @param BackgroundColor|null $backgroundColor Text background color
     * @param bool $isImportant If the text is important
     * @return void
     */
    public static function print(string $toPrint, Color $textColor, ?BackgroundColor $backgroundColor = null, bool $isImportant = false): void
    {
        $styles = [$textColor];

        if ($backgroundColor !== null) $styles[] = $backgroundColor;
        if ($isImportant) $styles[] = Text::Bold;

        self::printStyle(toPrint: $toPrint, compositeStyle: new Composite(...$styles));
    }

    /**
     * @brief Print the style text without any changes
     * @param string $toPrint Text to print
     * @param Composite $compositeStyle Text style
     * @param int $countOfLineBreak Count of line break after
     * @return void
     */
    public static function printStyle(string $toPrint, Composite $compositeStyle, int $countOfLineBreak = 0): void
    {
        echo (new Style)->apply(text: $toPrint, style: $compositeStyle);
        echo str_repeat(string: PHP_EOL, times: $countOfLineBreak);
    }
}