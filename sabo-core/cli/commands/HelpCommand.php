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
        $commands = $cli->getCommands();

        Printer::printStyle(
            toPrint: "> SABO CLI",
            compositeStyle: $themeConfig->getConfig(Theme::TITLE_STYLE->value),
            countOfLineBreak: 2
        );

        // vérification du cas d'affichage de l'aide d'une commande
        $searchedCommand = $cli->getArgumentManager()->find(optionName: "command");

        if($searchedCommand !== null){
            $searchedCommand = $searchedCommand->getArgumentValue();

            // commande non trouvée pas
            if(!array_key_exists(key: $searchedCommand,array: $commands) ){
                Printer::printStyle(
                    toPrint: "Commande <$searchedCommand> non trouvée",
                    compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
                );
                return;
            }

            // affichage de l'aide la commande
            Printer::printStyle(toPrint: "\t> ($searchedCommand)",compositeStyle: $basicStyle,countOfLineBreak: 1);

            foreach($commands[$searchedCommand]->getHelpLines() as $helpLine)
                Printer::printStyle(toPrint: "\t\t> $helpLine",compositeStyle: $notImportantStyle,countOfLineBreak: 1);

            return;
        }

        Printer::printStyle(
            toPrint: "> Liste des commandes",
            compositeStyle: $themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value),
            countOfLineBreak: 1
        );

        // tri des noms des commandes
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
            "php sabo $this->commandName",
            "Options optionnelles : ",
            "\t--command : Nom de la commande précise dont vous souhaitez l'aide"
        ];
    }
}