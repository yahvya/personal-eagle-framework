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
        $notImportantStyle = $themeConfig->getConfig(Theme::NOT_IMPORTANT_STYLE->value);
        $basicStyle = $themeConfig->getConfig(Theme::BASIC_TEXT_STYLE->value);

        Printer::printStyle(
            "> SABO CLI",
            $themeConfig->getConfig(Theme::TITLE_STYLE->value),
            2
        );

        Printer::printStyle(
            "> Liste des commandes",
            $themeConfig->getConfig(Theme::SPECIAL_TEXT_STYLE->value),
            1
        );

        // récupération et tri des noms des commandes
        $commands = $cli->getCommands();
        $commandsNames = array_keys($commands);
        sort($commandsNames);

        // affichage des commandes
        foreach($commandsNames as $name){
            Printer::printStyle("\t> ($name)",$basicStyle,1);

            foreach($commands[$name]->getHelpLines() as $helpLine)
                Printer::printStyle("\t\t> $helpLine",$notImportantStyle,1);
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