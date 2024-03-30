<?php

namespace SaboCore\Cli\Commands;

use SaboCore\Config\ConfigException;
use Throwable;

/**
 * @brief Commande de template d'un template
 * @author yahaya bathily https://github.com/yahvya/
 */
abstract class SaboTemplateUserCommand extends SaboCommand{
    /**
     * @const string Chemin du dossier des templates
     */
    public const string TEMPLATES_DIR_PATH = "/storage/templates";

    /**
     * @brief Crée un fichier à partir d'un template
     * @param string $templatePath Chemin du template à partir du dossier de stockage des templates
     * @param string $dstPath Chemin absolu de destination du fichier
     * @param array $replacements données à remplacer dans le template au format ["clé" → "remplacement"]
     * @return bool Si la création a bien réussi
     */
    protected function createFromTemplate(string $templatePath,string $dstPath,array $replacements):bool{
        try{
            // récupération du contenu du template
            $templateContent = @file_get_contents(filename: self::getCliRoot() . self::TEMPLATES_DIR_PATH . $templatePath);

            if($templateContent === false) return false;

            // remplacement des élements
            foreach($replacements as $key => $replace)
                $templateContent = str_replace(search: '{' . $key .'}',replace: $replace,subject: $templateContent);

            return @file_put_contents(filename: $dstPath,data: $templateContent) !== false;
        }
        catch(Throwable){
            return false;
        }
    }

    /**
     * @return string Le chemin root du dossier cli
     * @throws ConfigException en cas d'erreur de configuration
     */
    public static function getCliRoot():string{
        return APP_CONFIG->getConfig(name: "ROOT") . "/sabo-core/cli";
    }

    /**
     * @brief Formate le nom fourni au format class
     * @param string $baseName nom de base
     * @return string Le nom formaté
     */
    public static function formatNameForClass(string $baseName):string{
        return implode(
            "",
            array_map(
                callback: fn(string $part):string => ucfirst(string: strtolower(string: $part) ),
                array: explode(separator: " ",string: $baseName)
            )
        );
    }

    /**
     * @brief Recherche le namespace et le dossier d'une class (psr-4) à partir du dossier fourni récursivement
     * @param string $className Nom de la class
     * @param string $from Chemin de départ de recherche
     * @return array|null null si non trouvé ou, données au format ["namespace" → "..." ou null si non trouvé,"directory" → "..."]
     */
    public static function findClassDatas(string $className,string $from):array|null{
        $dirContent = @scandir(directory: $from);

        if($dirContent === false)
            $dirContent = [];

        $dirContent = array_diff($dirContent,[".",".."]);
        $fileKey = array_search(needle: "$className.php",haystack: $dirContent);

        // fichier trouvé, récupération du contenu
        if(is_int(value: $fileKey) ){
            $fileContent = @file_get_contents(filename: "$from/$className.php");

            // échec de lecture
            if($fileContent === false)
                return null;

            // récupération du namespace
            @preg_match(pattern: "#namespace (.*);#",subject: $fileContent,matches: $matches);

            return [
                "namespace" => $matches[1] ?? null,
                "directory" => $from
            ];
        }

        // recherche dans les potentiels sous dossiers
        foreach($dirContent as $contentName){
            $contentAbsolutePath = "$from/$contentName";

            if(!is_dir(filename: $contentAbsolutePath) )
                continue;

            $result = self::findClassDatas(className: $className,from: $contentAbsolutePath);

            if($result !== null)
                return $result;
        }

        return null;
    }
}