<?php

namespace Sabo\Helper;

/**
 * contient mes fonctions utiles
 */
abstract class Helper{
    /**
     * @param filepath chemin du fichier
     * @return bool retourne si le fichier existe
     */
    public static function fileExist(string $filepath):bool{
        return file_exists(ROOT . $filepath);
    }

    /**
     * require_once un fichier en partant de root
     * @param filepath le chemin du fichier
     * @return mixed le retour du require_once
     */
    public static function require(string $filepath):mixed{
        return require_once(ROOT . $filepath);
    }
}