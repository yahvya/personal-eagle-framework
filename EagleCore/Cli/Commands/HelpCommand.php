<?php

namespace Yahvya\EagleFramework\Cli\Commands;

use Override;
use Yahvya\EagleFramework\Cli\Cli\EagleFrameworkCLI;
use Yahvya\EagleFramework\Cli\Theme\Theme;
use Yahvya\EagleFramework\Utils\Printer\Printer;

/**
 * @brief CLI commands help command
 */
class HelpCommand extends EagleFrameworkCLICommand
{
    #[Override]
    public function execCommand(EagleFrameworkCLI $cli): void
    {
        $themeConfig = $cli->themeConfig;
        $notImportantStyle = $themeConfig->getConfig(name: Theme::NOT_IMPORTANT_STYLE->value);
        $basicStyle = $themeConfig->getConfig(name: Theme::BASIC_TEXT_STYLE->value);
        $commands = $cli->commands;

        Printer::printStyle(
            toPrint: "> EAGLE CLI",
            compositeStyle: $themeConfig->getConfig(Theme::TITLE_STYLE->value),
            countOfLineBreak: 2
        );

        $searchedCommand = $cli->argumentManager->find(optionName: "command");

        if ($searchedCommand !== null)
        {
            $searchedCommand = $searchedCommand->argumentValue;

            if (!array_key_exists(key: $searchedCommand, array: $commands))
            {
                Printer::printStyle(
                    toPrint: "Command <$searchedCommand> not found",
                    compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
                );
                return;
            }

            Printer::printStyle(toPrint: "\t> ($searchedCommand)", compositeStyle: $basicStyle, countOfLineBreak: 1);

            foreach ($commands[$searchedCommand]->getHelpLines() as $helpLine)
                Printer::printStyle(toPrint: "\t\t> $helpLine", compositeStyle: $notImportantStyle, countOfLineBreak: 1);

            return;
        }

        Printer::printStyle(
            toPrint: "> Commands list",
            compositeStyle: $themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value),
            countOfLineBreak: 1
        );

        $commandsNames = array_keys(array: $commands);
        sort(array: $commandsNames);

        foreach ($commandsNames as $name)
        {
            Printer::printStyle(toPrint: "\t> ($name)", compositeStyle: $basicStyle, countOfLineBreak: 1);

            foreach ($commands[$name]->getHelpLines() as $helpLine)
                Printer::printStyle(toPrint: "\t\t> $helpLine", compositeStyle: $notImportantStyle, countOfLineBreak: 1);
        }
    }

    #[Override]
    public function getHelpLines(): array
    {
        return [
            "Show the list of the commands and their associated help lines",
            "> php eagle $this->commandName",
            "Optional options :",
            "\t--command : The command name you want to display the help lines of - by default all commands are displayed",
        ];
    }
}