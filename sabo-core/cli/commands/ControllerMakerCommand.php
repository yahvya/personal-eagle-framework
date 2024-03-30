<?php

namespace SaboCore\Cli\Commands;

use Override;
use SaboCore\Cli\Cli\SaboCli;
use SaboCore\Cli\Theme\Theme;
use SaboCore\Utils\Printer\Printer;

/**
 * @brief Commande de création de controller
 * @author yahaya bathily https://github.com/yahvya
 */
class ControllerMakerCommand extends SaboTemplateUserCommand{
    /**
     * @const string Description par défaut du controller
     */
    protected const string CONTROLLER_DEFAULT_DESCRIPTION = "Controller";

    #[Override]
    public function execCommand(SaboCli $cli):void{
        $argumentManager = $cli->getArgumentManager();
        $themeConfig = $cli->getThemeConfig();

        // récupération de la configuration descriptive du controller
        $parentClass = $argumentManager->find(optionName: "parent")?->getArgumentValue() ?? "CustomController";
        $controllerName =  $this->getOptions($cli,"name")["name"];

        // controller non présent détaché
        $lowerControllerName = strtolower(string: $controllerName);

        if(
            !str_ends_with(haystack: $controllerName,needle: "Controller")
            &&
            !str_ends_with(haystack: $lowerControllerName,needle: " controller") &&
            !str_ends_with(haystack: $controllerName,needle: "controller")
        )
            $controllerName .= " controller";
        // controller présent mais attaché
        else
            $controllerName = substr(string: $controllerName,offset: 0,length: -strlen(string: "controller") ) . " controller";

        $controllerName = self::formatNameForClass(baseName: $controllerName);

        // récupération des données de la class parent
        $searchStartDirPath = APP_CONFIG->getConfig(name: "ROOT") . "/src/controllers";

        $parentClassConfig = self::findClassDatas(
            className: $parentClass,
            from: $searchStartDirPath
        );

        // échec de récupération de configuration du parent
        if($parentClassConfig === null){
            Printer::printStyle(
                toPrint: "Données de la class parent non trouvée dans l'un des dossiers / sous dossiers de <$searchStartDirPath>",
                compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
            );
            return;
        }

        // configuration de création
        ["namespace" => $namespace,"directory" => $parentClassDirPath] = $parentClassConfig;

        $replacements = [
            "controller-description" => $argumentManager->find(optionName: "description")?->getArgumentValue() ?? self::CONTROLLER_DEFAULT_DESCRIPTION,
            "parent-class" => $parentClass,
            "controller-import-config" => $namespace !== null ? "namespace $namespace;" : "",
            "controller-name" => $controllerName
        ];
        $destination = "$parentClassDirPath/$controllerName.php";

        // création du controller
        if(self::createFromTemplate(templatePath: "/controller-template.txt",dstPath: $destination,replacements: $replacements) ){
            Printer::printStyle(
                toPrint: "Controller <$controllerName> crée dans <$destination>",
                compositeStyle: $themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value)
            );
        }
        else{
            Printer::printStyle(
                toPrint: "Echec de création du controller, veuillez retenter",
                compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
            );
        }
    }

    #[Override]
    public function getHelpLines():array{
        return [
            "Crée un controller",
            "php sabo $this->commandName --name={nom du controller}",
            "Options requises :",
            "\t--name : Nom du controller",
            "Options optionnelles :",
            "\t--description : Description du controller - par défaut '". self::CONTROLLER_DEFAULT_DESCRIPTION ."'",
            "\t--parent : Nom du fichier contenant la class dont va extends le controller - par défaut le controller extends de CustomController. Recherche à partir du dossier src/controllers"
        ];
    }
}