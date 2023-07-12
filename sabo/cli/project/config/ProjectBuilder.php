<?php

namespace Sabo\Cli\Project\Config;

use Sabo\Cli\Project\Project\ProjectManagerCommand;
use Sabo\Cli\SaboCliCommand;

/**
 * constructeur de projet
 */
class ProjectBuilder implements ProjectManagerCommand{
    public function exec(int $argc,array $argv):bool{
        if(empty($argv) || $argv[0] !== "no-save"){
            if(!$this->createSaveCopy() ) return false;
        }

        if(!$this->deleteElementsOnBuild() ){
            SaboCliCommand::printMessage("La suppression des élements définis dans la conguration à échoué veuillez vérifier l'existance de tous les élements ou utilisez la sauvegarde faîtes");

            return false;
        }

        return true;
    }

    /**
     * crée une copie entière du projet 
     * @return bool si la copie a bien été faîtes
     */
    public function createSaveCopy():bool{
        while(true){
            SaboCliCommand::printMessage("Entrez le chemin du dossier qui contiendra la sauvegarde temporaire (dossier existant) : ");

            $path = trim(fgets(STDIN) );

            if(!str_ends_with($path,"/") && !str_ends_with($path,"\\") ) $path .= "/";

            if(!is_dir($path) ) 
                SaboCliCommand::printMessage("Le chemin fourni n'est pas un dossier");
            else
                break;
        }

        if(!$this->copyDirectoryIn($path,ROOT) ){
            SaboCliCommand::printMessage("La copie du projet a échoué");

            return false;
        }

        return true;
    }

    /**
     * copie un dossier dans l'autre
     * @param dstPath chemin de destination
     * @param fromDir dossier source
     * @return bool si le copie à réussi
     */
    private function copyDirectoryIn(string $dstPath,string $fromDir):bool{
        if(!is_dir($fromDir) || !is_dir($dstPath) ) return false;

        $dirContent = array_diff(scandir($fromDir),[".",".."]);

        foreach($dirContent as $path){
            $pathInSource = $fromDir . $path;

            // copie du fichier
            if(!is_dir($pathInSource) ){
                if(!copy($pathInSource,$dstPath . $path) ){
                    SaboCliCommand::printMessage("Une erreur s'est produite lors de la copie du fichier <{$pathInSource}>");

                    return false;
                }

                continue;
            }

            // copie du dossier
            if(!is_dir($dstPath . $path) ){
                if(!mkdir($dstPath . $path) ){
                    SaboCliCommand::printMessage("Une erreur s'est produite lors de la création du dossier <{$dstPath}{$path}>");

                    return false;
                }
            }

            if(!$this->copyDirectoryIn("{$dstPath}{$path}/","{$pathInSource}/") ) return false;
        }

        return true;

    }

    /**
     * supprime les élements définis dans le fichier de configuration
     * @return bool si les suppressions ont réussis
     */
    private function deleteElementsOnBuild():bool{
        $configFileContent = ProjectConfiguration::getConfigFileContent();

        if($configFileContent === null){
            SaboCliCommand::printMessage("Echec de récupération du contenu du fichier de configuration");

            return false;
        }

        $pathListToDelete = $configFileContent["toDeleteOnBuild"];

        foreach($pathListToDelete as $path){
            $path = ROOT . $path;
            
            if(is_dir($path) ) 
                $this->deleteDir(!str_ends_with($path,"/") && !str_ends_with($path,"\\") ? "{$path}/" : $path);
            else
                unlink($path);
        }
        
        return true;
    }

    /**
     * supprime un dossier
     * @param path le chemin
     * @return bool si la suppression à réussi
     */
    private function deleteDir(string $path):bool{
        if(!is_dir($path) ) return false;

        $dirContent = array_diff(scandir($path),[".",".."]);

        foreach($dirContent as $fileName){
            $completePath = $path . $fileName;

            if(is_dir($completePath) ){
                if(!$this->deleteDir("{$completePath}/") ) return false;
            }
            else unlink($completePath);
        }   

        rmdir($path);

        return true;
    }
}