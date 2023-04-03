<?php

namespace Sabo\Controller\TwigExtension;

use Twig\TwigFunction;

/**
 * extension permettant l'inclusion de fichiers js et css et favicon
 * fonctions [css,js,favicon]
 */
class SaboAssetsExtension extends SaboExtension{
    public function getFunctions():array{
        return [
            new TwigFunction("css",[$this,"includeCss"],["is_safe" => ["html" => true] ]),
            new TwigFunction("js",[$this,"includeJs"],["is_safe" => ["html" => true] ]),
            new TwigFunction("favicon",[$this,"includeFavicon"],["is_safe" => ["html" => true] ])
        ];
    }

    /**
     * inclus le fichier css passé à partir du dossier css/ du dossier parent du fichier sinon recherche dans public/css
     * @param fileName le nom du fichier css sans l'extension
     * @return string la balise link rel
     */
    public function includeCss(string $fileName):string{
        $folderPath = $this->getCurrentFileFolder();

        $filePath = file_exists($folderPath . "css/{$fileName}.css") ? str_replace(ROOT,"/",$folderPath) . "css/{$fileName}.css" : "/public/css/{$fileName}.css";

        return <<<HTML
            <link rel="stylesheet" href="{$filePath}">
        HTML;
    }

    /**
     * inclus le fichier js passé à partir du dossier js/ du dossier parent du fichier sinon recherche dans public/js
     * @param fileName le nom du fichier js sans l'extension
     * @param config tableau définissant les attributs defer et module
     * @return string la balise script src
     */
    public function includeJs(string $fileName,array $config = []):string{

        // ajout des attributs de la balise
        $attributes = [];

        $config = array_merge(["defer" => true,"module" => false],$config);

        if($config["defer"]) array_push($attributes,"defer");
        if($config["module"]) array_push($attributes,"type=\"module\"");

        $attributes = implode(" ",$attributes);

        $folderPath = $this->getCurrentFileFolder();

        $filePath = file_exists($folderPath . "js/{$fileName}.js") ? str_replace(ROOT,"/",$folderPath) . "js/{$fileName}.js" : "/public/js/{$fileName}.js";

        return <<<HTML
            <script src="{$filePath}" {$attributes}></script>
        HTML;
    }

    /**
     * inclus le favicon du site à partir de fichier /public/icons/favicon.ico
     * @return string la balise link rel favicon
     */
    public function includeFavicon():string{    
        return <<<HTML
            <link rel="icon" type="image/x-icon" href="/public/icons/favicon.ico" />
        HTML;
    }

    public static function initExtension():void{}
}