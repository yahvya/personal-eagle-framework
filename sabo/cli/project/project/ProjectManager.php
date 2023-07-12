<?php

namespace Sabo\Cli\Project\Project;

use Sabo\Cli\Project\Config\ProjectBuilder;
use Sabo\Cli\Project\Config\ProjectConfiguration;
use Sabo\Cli\SaboCliCommand;

/**
 * gestionnaire de projet
 */
class ProjectManager extends SaboCliCommand implements ProjectManagerCommand{
    /**
     * liste des commandes
     */
    private const COMMANDS_MAP = [
        [
            "description" => "Affiche l'aide des commandes",
            "execManager" => [self::class,"exec"],
            "command" => "project:command:help"
        ],
        [
            "description" => "Démarre la configuration du projet",
            "execManager" => [ProjectConfiguration::class,"exec"],
            "command" => "project:config"
        ],
        [
            "description" => "Initialise le projet (alias commande initialize)",
            "execManager" => [SaboInitializer::class,"execCommand"],
            "command" => "project:initialize"
        ],
        [
            "description" => "Construis le projet pour la production - Ajouter no-save pour ne pas créer une copie d'assurance du projet durant l'opération (non recommandé)",
            "execManager" => [ProjectBuilder::class,"exec"],
            "command" => "project:build"
        ]
    ];

    public function exec(int $argc,array $argv):bool{
        self::printMessage($this->getHelp() );
        
        return true;
    }

    public function execCommand(int $argc,array $argv,string $calledCommand):bool{
        foreach(self::COMMANDS_MAP as $commandDatas){
            if($commandDatas["command"] == $calledCommand){
                $commandDatas["execManager"][0] = new $commandDatas["execManager"][0]();

                return call_user_func_array($commandDatas["execManager"],[$argc,$argv,$calledCommand]);
            }
        }
    
        return false;
    }

    protected function getCommandDescription():string{
        return "Les commandes (project:command) permettent une gestion simplifié des phases du projet - utilisez la commande project:command:help pour afficher les commandes";
    }

    protected function getHelp():string{
        return self::buildHelpStr();
    }

    protected function isMyCommand(string $firstArg):bool{
        $commands = array_map(fn(array $commandDatas):string => $commandDatas["command"],self::COMMANDS_MAP);

        return in_array($firstArg,$commands);
    }

    /**
     * @return string la chaine d'aide
     */
    private static function buildHelpStr():string{
        return implode("\n\t",array_map(fn(array $datas):string => "({$datas["command"]}) : {$datas["description"]}",self::COMMANDS_MAP) );
    }
}