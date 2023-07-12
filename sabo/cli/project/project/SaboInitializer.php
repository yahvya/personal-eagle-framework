<?php

namespace Sabo\Cli\Project\Project;

use Sabo\Cli\SaboCliCommand;

/**
 * initialiseur de projet
 */
class SaboInitializer extends SaboCliCommand{
    /**
     * commande
     */
    public const MY_COMMAND = "initialize";

    public function execCommand(int $argc,array $argv,string $calledCommand):bool{
        `cd app && composer install && composer dumpautoload -o`;

        return true;
    }

    protected function getCommandDescription():string{
        return "(initialize) Initialise les élements pour commencer le développement à partir d'un projet sabo";
    }

    protected function getHelp():string{
        return "Ecrivez initialize pour lancer la commande";
    }

    protected function isMyCommand(string $firstArg):bool{
        return $firstArg == self::MY_COMMAND;
    }
}