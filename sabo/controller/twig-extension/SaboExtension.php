<?php

namespace Sabo\Controller\TwigExtension;

use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;
use Twig\Extension\AbstractExtension;

/**
 * parent des extensions du framework
 */
abstract class SaboExtension extends AbstractExtension{
    /**
     * liste des class d'extensions du framework
     */
    private static array $extensions = [
        SaboRouteExtension::class,
        SaboAssetsExtension::class,
        SaboDebugExtension::class
    ];

    /**
     * chemin du fichier sur lequel l'extension va travailler
     */
    protected string $currentFile;

    /**
     * fonction visant à initialiser les ressources nécéssaires à l'extension peut être vide
     */
    abstract public static function initExtension():void;

    /**
     * défini le fichier actuel
     * @param file le chemin du fichier
     */
    public function setCurrentFile(string $file):void{
        $this->currentFile = $file;
    }

    /**
     * @return string le chemin du dossier dans lequel se trouve le fichier
     */
    protected function getCurrentFileFolder():string{
        return dirname($this->currentFile) . "/";
    }   

    /**
     * @return array les extensions crée par sabo
     */
    public static function getExtensions():array{
        return self::$extensions;
    }
}