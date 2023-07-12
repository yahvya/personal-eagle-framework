<?php

namespace Sabo\Cli\Model;

use Sabo\Cli\Maker\FileMaker;

/**
 * créateur de model
 */
class ModelMaker extends FileMaker{
    /**
     * commande
     */
    public const MY_COMMAND = "make:model";

    /**
     * mot de fin de saisie
     */
    private const END_WORD = "<fin>";

    /**
     * formate le nom du model
     */
    private function formatName(string $givenName):string{
        foreach(["-","_"] as $sep){
            // placement des majuscule
            $givenName = implode("",array_map(fn(string $part):string => ucfirst($part) ,explode($sep,$givenName) ) );
        }

        $endWidthUpper = str_ends_with($givenName,"Model");

        if(!$endWidthUpper){
            if(str_ends_with($givenName,"model") ) 
                $givenName = substr($givenName,0,-10) . "Model";
            else
                $givenName .= "Model"; 
        }

        return $givenName;
    }

    /**
     * demande le nom de la table
     */
    private function askTableName():string{
        do{
            self::printMessage("Veuillez saisir le nom de la table : ");
            
            $tableName = trim(fgets(STDIN) );
        }while(strlen($tableName) < 1);
        
        return $tableName;
    }

    /**
     * demande la liste des attributs de la table
     */
    private function askAttributes():string{
        $attributes = "";

        $typesLink = [
            "s" => "string",
            "i" => "int",
            "f" => "float"
        ];

        $typesString = [];

        foreach($typesLink as $key => $value) array_push($typesString,"{$key} ({$value})");

        $typesString = implode(" - ",$typesString);

        self::printMessage("Ecrivez " . self::END_WORD . " pour terminer la saisie des attributs de la table\n\n");

        // lecture des attributs
        while(true){
            // demande du nom de la colonne
            do{
                self::printMessage("Saisissez la colonne lié à l'attribute : ");
                
                $colName = trim(fgets(STDIN) );
            }while(strlen($colName) < 1);

            if($colName == self::END_WORD) break;

            // demande du nom de la variable
            do{
                self::printMessage("Saisissez le nom de la variable : ");
                
                $varName = trim(fgets(STDIN) );
            }while(strlen($varName) < 1);

            // demande du type de la variable
            do{
                self::printMessage("Saisissez le type de la variable <{$typesString}> : ");
                
                $type = trim(fgets(STDIN) );
            }while(!array_key_exists($type,$typesLink) );

            $type = $typesLink[$type];

            self::printMessage("Champs nullable ? (entrée non - autre oui) : ");

            $isNullable = !$this->isEnter(fgets(STDIN) );

            if(!$isNullable){
                self::printMessage("Est-ce une clé primaire ? (entrée oui - autre non) : ");

                if($this->isEnter(fgets(STDIN) ) ){
                    self::printMessage("Auto-increment ? (entrée oui - autre non) : ");

                    $primaryKey = $this->isEnter(fgets(STDIN) ) ? ",new PrimaryKeyCond(true) " : ",new PrimaryKeyCond() ";
                }
                else $primaryKey = "";
            }

            $attributes .= $isNullable ? "\t#[TableColumn(\"{$colName}\",true)]\n\tprotected ?{$type} \${$varName} = null;\n\n" : "\t#[TableColumn(\"{$colName}\",false{$primaryKey})]\n\tprotected {$type} \${$varName};\n\n";

            echo "\n";
        }

        if(!empty($attributes) ) $attributes = substr($attributes,0,-2);

        return $attributes;
    }

    public function execCommand(int $argc,array $argv,string $calledCommand):bool{
        // alors nom manquant
        if($argc == 0){
            self::printMessage("Veuillez saisir au minimum le nom du model");

            return false;
        }

        list($modelName,$modelDescription,) = $argc > 1 ? $argv : [$argv[0],"model"];

        $modelName = $this->formatName($modelName);

        return $this->createFileIn(ROOT . "src/model/{$modelName}.php",[
            "model-name" => $modelName,
            "model-description" => $modelDescription,
            "table-name" => $this->askTableName(),
            "attributes" => $this->askAttributes()
        ]);
    }

    protected function getHelp():string{
        return "commande(make:model): arg1(nom du model) arg2(optionnel - description du model)";
    }

    protected function getCommandDescription():string{
        return "(make:model) Permet de créer un model";
    }
    
    protected function isMyCommand(string $firstArg):bool{
        return $firstArg == self::MY_COMMAND;
    }

    protected function getModelFilePath():string{
        return "model-model.txt";
    }
}