<?php

namespace SaboCore\Utils\Printer;

use BeBat\ConsoleColor\Style;
use BeBat\ConsoleColor\Style\Color;
use BeBat\ConsoleColor\Style\BackgroundColor;
use BeBat\ConsoleColor\Style\Composite;
use BeBat\ConsoleColor\Style\Text;

/**
 * @brief Afficheur terminal
 * @author yahaya bathily https://github.com/yahvya/
 */
abstract class Printer{
    /**
     * @brief Affiche le texte fournie sans modification
     * @param string $toPrint texte à afficher
     * @param Color $textColor couleur du texte
     * @param BackgroundColor|null $backgroundColor couleur de fond du texte
     * @param bool $isImportant si le texte est important
     * @return void
     */
    public static function print(string $toPrint,Color $textColor,?BackgroundColor $backgroundColor = null,bool $isImportant = false):void{
        $styles = [$textColor];

        if($backgroundColor !== null) $styles[] = $backgroundColor;
        if($isImportant) $styles[] = Text::Bold;

        self::printStyle(toPrint: $toPrint,compositeStyle: new Composite(...$styles) );
    }

    /**
     * @brief Affiche le texte fournie sans modification
     * @param string $toPrint texte à afficher
     * @param Composite $compositeStyle style du texte
     * @param int $countOfLineBreak nombre de sauts de ligne après
     * @return void
     */
    public static function printStyle(string $toPrint,Composite $compositeStyle,int $countOfLineBreak = 0):void{
        echo (new Style)->apply(text: $toPrint,style: $compositeStyle);
        echo str_repeat(string: PHP_EOL,times: $countOfLineBreak);
    }
}