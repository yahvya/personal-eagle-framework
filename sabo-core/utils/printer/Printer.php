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

        self::printStyle($toPrint,new Composite(...$styles) );
    }

    /**
     * @brief Affiche le texte fournie sans modification
     * @param string $toPrint texte à afficher
     * @param Composite $compositeStyle style du texte
     * @return void
     */
    public static function printStyle(string $toPrint,Composite $compositeStyle):void{
        echo (new Style)->apply($toPrint,$compositeStyle);
    }
}