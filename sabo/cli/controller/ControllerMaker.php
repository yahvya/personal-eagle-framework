<?php

namespace Sabo\Cli\Controller;

use Sabo\Cli\Maker\FileMaker;

/**
 * créateur de controller
 */
class ControllerMaker extends FileMaker{
    /**
     * commande
     */
    public const MY_COMMAND = "make:controller";

    /**
     * formate le nom du controller
     */
    private function formatName(string $givenName):string{
        foreach(["-","_"] as $sep){
            // placement des majuscule
            $givenName = implode("",array_map(fn(string $part):string => ucfirst($part) ,explode($sep,$givenName) ) );
        }

        $endWidthUpper = str_ends_with($givenName,"Controller");

        if(!$endWidthUpper){
            if(str_ends_with($givenName,"controller") ) 
                $givenName = substr($givenName,0,-10) . "Controller";
            else
                $givenName .= "Controller"; 
        }

        return $givenName;
    }

    public function execCommand(int $argc,array $argv,string $calledCommand):bool{
        // alors nom manquant
        if($argc == 0){
            self::printMessage("Veuillez saisir au minimum le nom du controller");

            return false;
        }

        list($controllerName,$controllerDescription,) = $argc > 1 ? $argv : [$argv[0],"controller"];

        $controllerName = $this->formatName($controllerName);

        return $this->createFileIn(ROOT . "src/controller/{$controllerName}.php",[
            "controller-name" => $controllerName,
            "controller-description" => $controllerDescription
        ]);
    }

    protected function getHelp():string{
        return "commande(make:controller): arg1(nom du controller) arg2(optionnel - description du controller)";
    }

    protected function getCommandDescription():string{
        return "(make:controller) Permet de créer un controller";
    }

    protected function isMyCommand(string $firstArg):bool{
        return $firstArg == self::MY_COMMAND;
    }

    protected function getModelFilePath():string{
        return "controller-model.txt";
    }
}