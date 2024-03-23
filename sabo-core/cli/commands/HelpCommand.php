<?php

namespace SaboCore\Cli\Commands;

use Override;
use SaboCore\Cli\Cli\SaboCli;
use SaboCore\Cli\Theme\Theme;
use SaboCore\Utils\Printer\Printer;

/**
 * @brief Commande d'affichage d'aide
 * @author yahaya bathily https://github.com/yahvya/
 */
class HelpCommand extends SaboCommand {
    #[Override]
    public function execCommand(SaboCli $cli): void{
        $themeConfig = $cli->getThemeConfig();
        $notImportantStyle = $themeConfig->getConfig(name: Theme::NOT_IMPORTANT_STYLE->value);
        $basicStyle = $themeConfig->getConfig(name: Theme::BASIC_TEXT_STYLE->value);

        Printer::printStyle(
            toPrint: "> SABO CLI",
            compositeStyle: $themeConfig->getConfig(Theme::TITLE_STYLE->value),
            countOfLineBreak: 2
        );

        Printer::printStyle(
            toPrint: "> Liste des commandes",
            compositeStyle: $themeConfig->getConfig(Theme::SPECIAL_TEXT_STYLE->value),
            countOfLineBreak: 1
        );

        // récupération et tri des noms des commandes
        $commands = $cli->getCommands();
        $commandsNames = array_keys(array: $commands);
        sort(array: $commandsNames);

        // affichage des commandes
        foreach($commandsNames as $name){
            Printer::printStyle(toPrint: "\t> ($name)",compositeStyle: $basicStyle,countOfLineBreak: 1);

            foreach($commands[$name]->getHelpLines() as $helpLine)
                Printer::printStyle(toPrint: "\t\t> $helpLine",compositeStyle: $notImportantStyle,countOfLineBreak: 1);
        }
    }

    #[Override]
    public function getHelpLines(): array{
        return [
            "Affiche la liste des commandes",
            "php sabo $this->commandName"
        ];
    }
}