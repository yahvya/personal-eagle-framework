<?php

namespace Sabo\Cli\Maker;

use Sabo\Cli\SaboCliCommand;

/**
 * représente les class générant un fichier
 */
abstract class FileMaker extends SaboCliCommand{
    /**
     * chemin des ressources model
     */
    private const MODELS_PATH = ROOT . "sabo/cli/resources/model/";

    /**
     * crée le fichier en remplaçant
     * @param filePath chemin du fichier destination
     * @param toReplace les données à remplacer dans le modèle
     */
    public function createFileIn(string $filePath,array $toReplace):bool{
        if(file_exists($filePath) ){
            SaboCliCommand::printMessage("Le fichier existe déjà sur le chemin ({$filePath}), voulez vous l'écraser (entrée oui - autre non) : ");

            if(!$this->isEnter(fgets(STDIN) ) ) return false;
        }

        // lecture du modèle à utiliser
        $modelContent = @file_get_contents(self::MODELS_PATH . $this->getModelFilePath() );

        if($modelContent === false) return false;

        // remplacement des valeurs à changer
        foreach($toReplace as $toReplaceString => $replaceValue) $modelContent = str_replace("{{$toReplaceString}}",$replaceValue,$modelContent);

        return @file_put_contents($filePath,$modelContent);
    }

    /**
     * @return string le chemin du modèle
     */
    protected abstract function getModelFilePath():string; 
}