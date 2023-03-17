<?php

namespace Sabo\Helper;

/**
 * aide à la gestion des fichiers
 */
class FileHelper{
    /**
     * chemin du fichier à partir de la racine
     */
    private string $filepath;

    /**
     * tableau représentant les données du fichier
     * @format clés du tableau ["extension","folder"]
     */
    private array $fileDatas;

    public function __construct(string $filepath){
        $this->filepath = ROOT . $filepath;

        $this->setFileDatas();
    }

    /**
     * @json sous forme de tableau
     * @env sous forme de tableau
     * @default sous forme de texte
     * @return mixed le contenu du fichier en fonction de son type d'extension ou null en cas d'échec
     */
    public function getFileContent():mixed{
        if(!file_exists($this->filepath) || ($fileContent = @file_get_contents($this->filepath) ) == null) return null;

        if($fileContent == false) return null;

        switch($this->fileDatas["extension"]){
            case ".json":
                $jsonData = @json_decode($fileContent,true );

                return $jsonData;
            ; break;

            case ".env":
                $result = [];

                foreach(explode("\n",$fileContent) as $line){
                    list($key,$content) = explode("=",$line);

                    $result[trim($key)] = trim($content);
                }

                return $result;
            ; break;

            default:
                return $fileContent;
            ;
        }
    }

    /**
     * défini les données du tableau fileDatas
     */
    private function setFileDatas():void{
        $pathPart = explode(".",$this->filepath);

        $this->fileDatas = [
            "extension" => "." . array_pop($pathPart),
            "folder" => dirname($this->filepath) . "\\"
        ];
    }

    /**
     * @return array les données sur le fichier
     */
    public function getFileDatas():array{
        return $this->fileDatas;
    }

    /**
     * @return string l'extension du fichier lié
     */
    public function getExtension():string{
        return $this->fileDatas["extension"];
    }
}