<?php

namespace Sabo\Cli\Extension;

use Sabo\Cli\SaboCliCommand;
use ZipArchive;

/**
 * ajout d'extension
 */
class ExtensionAdd extends SaboCliCommand{
    /**
     * commande
     */
    public const MY_COMMAND = "extension:add";

    /**
     * chemin de destination des extensions
     */
    private const EXTENSIONS_DEFAULT_DIR = ROOT . "src/sabo-extensions";

    /**
     * extension des fichiers extensions
     */
    private const EXTENSIONS_FILE_EXTENSION = ".zip";
    
    public function execCommand(int $argc,array $argv,string $calledCommand):bool{
        $extensionZipFilePath = $this->getExtensionZipFilePath();
        $dstDir = $this->getDstDir(); 

        if($dstDir != self::EXTENSIONS_DEFAULT_DIR . "/") {
            $dstDir = ROOT . (str_starts_with($dstDir,"/") || str_starts_with($dstDir,"/") ? substr($dstDir,1) : $dstDir);
        }

        // ouverture du fichier zip
        $zip = new ZipArchive();

        if($zip->open($extensionZipFilePath) !== true){
            SaboCliCommand::printMessage("Echec d'ouverture du fichier extension");

            return false;
        }

        // création du dossier de destination
        if(!is_dir($dstDir) ){
            if(!@mkdir($dstDir,recursive: true) ){
                SaboCliCommand::printMessage("Echec de création du dossier de destination");

                return false;
            }
        }

        // récupération du nom du dossier
        $dirname = $zip->getNameIndex(0);

        if($dirname === false){
            SaboCliCommand::printMessage("Le zip est mal formé");

            return false;
        }

        $dirname = explode("/",str_replace("\\","/",$dirname) )[0];

        // extraction du zip
        if(!$zip->extractTo($dstDir) ){
            SaboCliCommand::printMessage("Echec de l'extraction du fichier, veuillez retenter");

            return false;
        }

        // mise à jour de classmap
        if(!$this->updateClassmap($dstDir) ){
            $this->deleteDir($dstDir . $dirname);

            SaboCliCommand::printMessage("Une erreur s'est produite lors de la mise à jour de votre fichier composer.json veuillez reprendre l'opération");

            return false;
        }

        return true;
    }

    protected function getCommandDescription():string{
        return "(extension:add) Utilitaire facilitant l'ajout d'extension";
    }

    protected function getHelp():string{
        return "(extension:add) cette commande doit être appellé de la racine du projet";
    }

    protected function isMyCommand(string $firstArg):bool{
        return $firstArg == self::MY_COMMAND;
    }

    /**
     * demande et récupère le chemin de destination de l'extension
     * @return string le chemin choisi
     */
    private function getDstDir():string{
        SaboCliCommand::printMessage("Saississez le chemin du dossier contenant vos extensions à partir de la racine de votre projet - appuyez sur entrée pour utiliser le dossier par défaut (" . self::EXTENSIONS_DEFAULT_DIR . ") : ");

        $input = @fgets(STDIN);

        $directory = $this->isEnter($input) ? self::EXTENSIONS_DEFAULT_DIR : trim($input);

        if(!str_ends_with($directory,"\\") && !str_ends_with($directory,"/") ) $directory .= "/";

        return $directory;
    }

    /**
     * demande et récupère le chemin du fichier zip de l'extension
     */
    private function getExtensionZipFilePath():string{
        $extensionLen = strlen(self::EXTENSIONS_FILE_EXTENSION);

        do{
            SaboCliCommand::printMessage("Entrez le chemin complet du fichier de l'extension téléchargé : ");

            $path = trim(fgets(STDIN) );
        }while(!str_ends_with($path,self::EXTENSIONS_FILE_EXTENSION) || strlen($path) <= $extensionLen);

        return $path;
    }

    /**
     * met à jour le fichier composer.json
     * @param dstDir le dossier conteneur des extensions
     * @return bool si la mise à jour à réussi
     */
    private function updateClassmap($dstDir):bool{
        $composerFilePath = ROOT . "app/composer.json";

        // chargement du contenu json
        $composerContent = @file_get_contents($composerFilePath);

        if($composerContent === false){
            SaboCliCommand::printMessage("Echec de chargement du fichier composer");

            return false;
        }

        $composerContent = $baseContent = @json_decode($composerContent,true);

        if($composerContent === null){
            SaboCliCommand::printMessage("Echec de chargement du fichier composer");

            return false;
        }
    
        if(empty($composerContent["autoload"]["classmap"]) ) $composerContent["autoload"]["classmap"] = [];

        // création du chemin à partir du dossier app
        $dstDir = "../" . substr($dstDir,strlen(ROOT) );

        if(!in_array($dstDir,$composerContent["autoload"]["classmap"]) ){
            array_push($composerContent["autoload"]["classmap"],$dstDir);
           
            if(@file_put_contents($composerFilePath,json_encode($composerContent,JSON_PRETTY_PRINT) ) === false) return false;

            `cd app && composer dumpautoload -o`;
        }

        return true;
    }

    /**
     * supprime un dossier
     * @param dir le chemin du dossier
     */
    private function deleteDir(string $dir):void{ 
        if(is_dir($dir) ){
            if(!str_ends_with($dir,"\\") && !str_ends_with($dir,"/") ) $dir .= "/";

            $dirElementsList = array_diff(scandir($dir),[".",".."]);

            foreach($dirElementsList as $dirElement) $this->deleteDir($dir . $dirElement);

            @rmdir($dir);
        }
        else @unlink($dir);
    }
}