<?php

namespace Sabo\Cli\Project\Config;

use Sabo\Cli\Project\Project\ProjectManagerCommand;
use Sabo\Cli\SaboCliCommand;

/**
 * gestionnaire de configuration du projet
 */
class ProjectConfiguration implements ProjectManagerCommand{
    /**
     * chemin du fichier gérant la coniguration
     */
    public const CONFIG_FILEPATH = "sabo/cli/resources/project/config/config-file.json";

    /**
     * choix du menu
     */
    private const CHOICE_MAP = [
        [
            "title" => "Afficher la configuration",
            "exec" => "printConfig"
        ],
        [
            "title" => "Modifier la configuration la configuration",
            "exec" => "updateConfig"
        ]
    ];

    /**
     * contenu du fichier de configuration
     */
    private array $configFileContent;

    /**
     * nombre d'arguments
     */
    private int $argc;

    /**
     * arguments
     */
    private array $argv;

    public function exec(int $argc, array $argv):bool{
        $this->argc = $argc;
        $this->argv = $argv;

        return $this->setConfigFileDatas() && $this->readChoice();
    }

    /**
     * affiche le menu de choix et gère le choix fait
     */
    public function readChoice():bool{
        $exit = false;

        while(!$exit){
            $input = $this->getChoiceFrom(self::CHOICE_MAP);

            if(is_numeric($input) && ($key = intval($input) ) !== 0){
                if(array_key_exists($key - 1,self::CHOICE_MAP) ) 
                    call_user_func([$this,self::CHOICE_MAP[$key - 1]["exec"] ]);
                else
                    SaboCliCommand::printMessage("(action non trouvé)\n\n");
            }   
            else $exit = true;
        }

        return true;
    }

    /**
     * récupère et stocke les données du fichier de configuration du projet
     * @return bool si les données ont bien été récupéré
     */
    private function setConfigFileDatas():bool{
        $fileContent = self::getConfigFileContent();

        if($fileContent != null){
            $this->configFileContent = $fileContent;

            return true;
        }

        return false;
    }

    /**
     * affiche la configuration
     * @return ProjectConfiguration this
     */
    private function printConfig():ProjectConfiguration{
        echo "\nElements à supprimer au build du projet :\n";

        foreach($this->configFileContent["toDeleteOnBuild"] as $toDelete) echo "\t{$toDelete}\n";

        return $this;
    }

    /**
     * débute la modification de la configuration
     * @return ProjectConfiguration this
     */
    private function updateConfig():ProjectConfiguration{
        $choiceMap = [
            [
                "title" => "Retirer un/des élements à supprimer au build du projet",
                "exec" => "removeElementsInBuild"
            ],
            [
                "title" => "Ajouter un/des éléments à supprimer au build du projet",
                "exec" => "addElementsInBuild"
            ]
        ];

        $input = $this->getChoiceFrom($choiceMap);

        if(is_numeric($input) && array_key_exists(($key = intval($input) ) - 1,$choiceMap) ) call_user_func([$this,$choiceMap[$key - 1]["exec"]]);

        return $this;
    }

    /**
     * suprimer des élements à supprimer à la configuration
     * @return ProjectConfiguration this
     */
    private function removeElementsInBuild():ProjectConfiguration{
        echo "\nListe des élements :\n";

        $toDeleteOnBuild = $this->configFileContent["toDeleteOnBuild"];

        foreach($toDeleteOnBuild as $key => $fileName){
            $id = $key + 1;

            echo "\t- ({$id}) {$fileName}\n";
        }

        echo "\nVeuillez saisir les clés séparés d'une virgule : ";

        $toGet = explode(",",trim(fgets(STDIN) ) );

        // suppression des éléments
        foreach($toGet as $index){
            if(!is_numeric($index) ) continue;

            $index = intval($index) - 1;

            if(!array_key_exists($index,$toDeleteOnBuild) ) continue;

            unset($toDeleteOnBuild[$index]);
        }

        // mise à jour
        $toDeleteOnBuild = array_values($toDeleteOnBuild);

        $this->configFileContent["toDeleteOnBuild"] = $toDeleteOnBuild;

        $newFileContent = @json_encode($this->configFileContent,JSON_PRETTY_PRINT);

        if($newFileContent !== false){
            if(!@file_put_contents(ROOT . self::CONFIG_FILEPATH,$newFileContent) ) $this->setConfigFileDatas();
        }

        return $this;
    }

    /**
     * ajoute des élements à supprimer à la configuration
     * @return ProjectConfiguration this
     */
    private function addElementsInBuild():ProjectConfiguration{
        echo "\nSaisissez les chemins à ajouter (à partir de la racine du projet)\n";

        $toDeleteOnBuild = $this->configFileContent["toDeleteOnBuild"];

        while(true){
            echo "\n\tEntrez le chemin (0) pour quitter : ";

            $input = trim(fgets(STDIN) );

            if(is_numeric($input) && intval($input) == 0) break;

            if(in_array($input,$toDeleteOnBuild) ){
                echo "\n\tLe chemin est déjà parmis la liste des chemins";

                continue;
            }

            array_push($toDeleteOnBuild,$input);
        }

        $this->configFileContent["toDeleteOnBuild"] = $toDeleteOnBuild;

        $newFileContent = @json_encode($this->configFileContent,JSON_PRETTY_PRINT);

        if($newFileContent !== false){
            if(!@file_put_contents(ROOT . self::CONFIG_FILEPATH,$newFileContent) ) $this->setConfigFileDatas();
        }

        return $this;
    }

    /**
     * récupère la saisie utilisateur sur une liste de choix
     * @param from la liste de choix
     * @return string le choix fait
     */
    private function getChoiceFrom(array $from):string{
        echo "\nVeuillez choisir l'action à faire (0 pour quitter) ou\n\n";

        foreach($from as $key => $choiceData){
            $id = $key + 1;

            echo "\t({$id}) pour {$choiceData["title"]}\n";
        }

        echo "\nEntrez l'action à faire << ";

        return trim(fgets(STDIN) );
    }

    /**
     * récupère le contenu du fichier de configuration
     * 
     */
    public static function getConfigFileContent():?array{
        $configFileDatas = @file_get_contents(ROOT . self::CONFIG_FILEPATH);

        if($configFileDatas === false) return false;

        $fileContent = @json_decode($configFileDatas,true);

        return gettype($fileContent) == "array" ? $fileContent : null;
    }
}