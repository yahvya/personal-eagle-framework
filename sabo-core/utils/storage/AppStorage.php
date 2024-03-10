<?php

namespace SaboCore\Utils\Storage;

use SaboCore\Config\FrameworkConfig;
use SaboCore\Routing\Application\Application;
use Throwable;

/**
 * @brief Gestionnaire de stockage de l'application
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class AppStorage{
    /**
     * @brief Réalise une copie du fichier dans la destination de stockage
     * @param string $storagePath chemin avec comme racine le dossier de stockage du projet
     * @param string $fileBasePath chemin complet du fichier à copier
     * @param bool $createFoldersIfNotExists si true et que le nouveau chemin contient des dossiers inexistants, ils seront créés
     * @return bool
     */
    public static function storeClassicFile(string $storagePath,string $fileBasePath,bool $createFoldersIfNotExists = true):bool{
        try{
            $storagePath = self::buildStorageCompletePath($storagePath);
            $dirname = @dirname($storagePath);

            // création du dossier résultat s'il n'existe pas
            if($createFoldersIfNotExists){
                if(!@mkdir($dirname,recursive: true) )
                    return false;
            }

            return @copy($fileBasePath,$storagePath);
        }
        catch(Throwable){
            return false;
        }
    }

    /**
     * @brief Stock le contenu fourni dans la destination de stockage
     * @param string $storagePath chemin avec comme racine le dossier de stockage du projet
     * @param string $content le contenu à stocker
     * @param bool $createFoldersIfNotExists si true et que le nouveau chemin contient des dossiers inexistants, ils seront créés
     * @return bool
     */
    public static function storeContent(string $storagePath,string $content,bool $createFoldersIfNotExists = true):bool{
        try{
            $storagePath = self::buildStorageCompletePath($storagePath);
            $dirname = @dirname($storagePath);

            // création du dossier résultat s'il n'existe pas
            if($createFoldersIfNotExists){
                if(!@mkdir($dirname,recursive: true) )
                    return false;
            }

            return @file_put_contents($storagePath,$content) !== false;
        }
        catch(Throwable){
            return false;
        }
    }

    /**
     * @brief Upload le fichier formulaire dans la destination de stockage
     * @param string $storagePath chemin avec comme racine le dossier de stockage du projet
     * @param string $fileTmpName tmp_name associé au fichier
     * @param bool $createFoldersIfNotExists si true et que le nouveau chemin contient des dossiers inexistants, ils seront créés
     * @return bool
     */
    public static function storeFormFile(string $storagePath, string $fileTmpName, bool $createFoldersIfNotExists = true):bool{
        try{
            $storagePath = self::buildStorageCompletePath($storagePath);
            $dirname = @dirname($storagePath);

            // création du dossier résultat s'il n'existe pas
            if($createFoldersIfNotExists){
                if(!@mkdir($dirname,recursive: true) )
                    return false;
            }

            return @move_uploaded_file($fileTmpName,$storagePath);
        }
        catch(Throwable){
            return false;
        }
    }

    /**
     * @brief Construis le chemin absolu de stockage
     * @param string $pathFromStorage chemin avec comme racine le dossier de stockage
     * @return string le chemin complet de stockage
     */
    public static function buildStorageCompletePath(string $pathFromStorage):string{
        try{
            $completePath = Application::getFrameworkConfig()->getConfig(FrameworkConfig::STORAGE_DIR_PATH->value);

            if(str_ends_with($completePath,"/") ) $completePath = substr($completePath,0,-1);
            if(!str_starts_with($pathFromStorage,"/") ) $pathFromStorage = "/$pathFromStorage";

            return $completePath . $pathFromStorage;
        }
        catch(Throwable){
            return $pathFromStorage;
        }
    }
}