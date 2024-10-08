<?php

namespace SaboCore\SaboCli\Commands\Help;

use Override;
use SaboCore\SaboCli\ArgsParser\Parser;
use SaboCore\SaboCli\Commands\Commands\SaboCliCommand;

/**
 * @brief help command - allow to print commands list and help
 */
class Help extends SaboCliCommand{
    #[Override]
    public function executeCommand(Parser $parser): bool{
        $commands = self::$commands;

        if(
            $parser->optionExist(option: "command") ||
            $parser->optionExist(option: "C")
        ){
            $commandName = $parser->getOptionValue(option: "command") ?? $parser->getOptionValue(option: "C");

            $commands = array_key_exists(key: $commandName,array: self::$commands) ?
                [$commandName => self::$commands[$commandName]] : self::$commands;
        }

        self::printCommandsList(commands: $commands);

        return true;
    }
}