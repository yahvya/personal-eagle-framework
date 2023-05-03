<?php

namespace Sabo\Cli;

/**
 * initialiseur de projet
 */
class SaboInitializer extends SaboCliCommand{
    protected function execCommand(int $argc,array $argv):bool{
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
        return $firstArg == "initialize";
    }
}