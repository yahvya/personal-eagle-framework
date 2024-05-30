<?php

namespace SaboCore\Cli\Commands;

use Override;
use SaboCore\Cli\Cli\SaboCli;
use SaboCore\Cli\Theme\Theme;
use SaboCore\Config\ConfigException;
use SaboCore\Utils\Printer\Printer;

/**
 * @brief Commande de lancement de serveur
 * @author yahaya bathily https://github.com/yahvya
 */
class LaunchServerCommand extends SaboCommand{
    /**
     * @brief Port par défaut
     */
    protected const string DEFAULT_PORT = "8080";

    /**
     * @brief Hôte par défaut
     */
    protected const string DEFAULT_HOST = "127.0.0.1";

    /**
     * @brief Séparateur accepté sur les fichiers
     */
    protected const string FILES_SEPARATOR = ",";

    /**
     * @brief Extensions de fichier par défaut écoutées
     */
    protected const array DEFAULT_FILE_TYPES = ["php","js","css","twig","blade"];

    /**
     * @brief Commande de synchronisation
     */
    protected const string SYNC_COMMAND_NAME = "browser-sync";

    #[Override]
    public function execCommand(SaboCli $cli): void{
        $themeConfig = $cli->getThemeConfig();

        // récupération des options
        $argumentManager = $cli->getArgumentManager();

        $port = $argumentManager->find(optionName: "port")?->getArgumentValue() ?? self::DEFAULT_PORT;
        $host = $argumentManager->find(optionName: "host")?->getArgumentValue() ?? self::DEFAULT_HOST;
        $sync = $argumentManager->find(optionName: "sync");

        $link = "$host:$port";
        $rooter = APP_CONFIG->getConfig(name: "ROOT") . "/sabo-core/index.php";

        // vérification de l'utilisation de la synchronisation
        if($sync !== null){
            if(!self::manageSyncRequirements(cli: $cli) ){
                Printer::printStyle(
                    toPrint: "Echec de traitement de la commande de synchronisation",
                    compositeStyle: $cli->getThemeConfig()->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
                );

                return;
            }

            // récupération des extensions à traiter et formatage
            $extensions = explode(separator: self::FILES_SEPARATOR,string: $sync->getArgumentValue());
            $extensions = implode(
                separator: ",",
                array: array_map(
                    callback: fn(string $extension):string => "**/*.$extension",
                    array: $extensions[0] === "default" ? self::DEFAULT_FILE_TYPES : $extensions
                )
            );

            // lancement de la commande
            $syncProcess = popen(command:  self::SYNC_COMMAND_NAME . " start --proxy $link --files \"$extensions\"",mode: "r");

            if($syncProcess === false){
                Printer::printStyle(
                    toPrint: "Echec de lancement de la synchronisation",
                    compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
                );

                return;
            }
        }

        Printer::printStyle(
            toPrint: "Lancement du serveur ($link)",
            compositeStyle: $themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value),
            countOfLineBreak: 1
        );

        $serverProcess = @popen(command: "php -S $link $rooter",mode: "r");

        if($serverProcess === false){
            Printer::printStyle(
                toPrint: "Echec de lancement du serveur",
                compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
            );

            return;
        }

        // lecture des sorties
        while(true){
            if(isset($syncProcess)){
                while(($syncLine = fgets(stream: $syncProcess)) !== false)
                    print($syncLine);
            }

            while(($processLine = fgets(stream: $serverProcess)) !== false)
                print($processLine);
        }
    }

    #[Override]
    public function getHelpLines(): array{
        return [
            "Lance le serveur de développement - Port par défaut (" . self::DEFAULT_PORT . ") - Hôte par défaut (". self::DEFAULT_HOST .")",
            "php sabo $this->commandName",
            "Options optionnelles",
            "\t--port : Numéro du port",
            "\t--host : Adresse hôte",
            "\t--sync: Si l'option est présente la commande <browser-sync> sera utilisée (npm est nécessaire pour l'installation de la commande)",
            "\t\tVous pouvez spécifier comme valeur les types de fichiers à écouter séparés de <". self::FILES_SEPARATOR .">. Types par défaut : (" . implode(separator: ",",array: self::DEFAULT_FILE_TYPES) . ") sinon <default> comme valeur"
        ];
    }

    /**
     * @brief Vérifie si la commande de synchronisation est présente sinon tente de l'installer
     * @param SaboCli $cli Cli
     * @return bool si la commande est disponible d'utilisation
     * @throws ConfigException en cas d'erreur de configuration
     */
    public static function manageSyncRequirements(SaboCli $cli):bool{
        // vérification d'existence de la commande
        if(@exec(command: "npm list -g --depth=0 --parseable=true",output: $result) === false)
            return false;

        if(empty($result))
            return false;

        if(!empty(
            array_filter(
                array: $result,
                callback: fn(string $line):bool => str_contains(haystack: $line,needle: self::SYNC_COMMAND_NAME)
            )
        ))
            return true;

        // installation de la commande
        $success = @system(command: "npm install -g ". self::SYNC_COMMAND_NAME,result_code: $resultCode) === false || $resultCode !== 0;

        if($success){
            Printer::printStyle(
                toPrint: "Installation de la commande <" . self::SYNC_COMMAND_NAME .">",
                compositeStyle: $cli->getThemeConfig()->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value)
            );

            return true;
        }
        else{
            Printer::printStyle(
                toPrint: "Echec de l'installation de la commande <" . self::SYNC_COMMAND_NAME .">",
                compositeStyle: $cli->getThemeConfig()->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
            );

            return false;
        }
    }
}
