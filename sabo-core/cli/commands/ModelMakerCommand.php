<?php

namespace SaboCore\Cli\Commands;

use Override;
use SaboCore\Cli\Cli\SaboCli;
use SaboCore\Cli\Theme\Theme;
use SaboCore\Utils\Printer\Printer;

/**
 * @brief Commande de création de model
 * @author yahaya bathily https://github.com/yahvya
 */
class ModelMakerCommand extends SaboTemplateUserCommand{
    #[Override]
    public function execCommand(SaboCli $cli):void{
        $argumentManager = $cli->getArgumentManager();
        $themeConfig = $cli->getThemeConfig();

        // récupération de la configuration descriptive du model
        $parentClass = $argumentManager->find(optionName: "parent")?->getArgumentValue() ?? "CustomModel";
        [
            "name" => $modelName,
            "description" => $description,
            "table" => $tableName
        ] =  $this->getOptions($cli,"name","description","table");

        // model non présent détaché
        $lowerModelName = strtolower(string: $modelName);

        if(
            !str_ends_with(haystack: $modelName,needle: "Model")
            &&
            !str_ends_with(haystack: $lowerModelName,needle: " model") &&
            !str_ends_with(haystack: $modelName,needle: "model")
        )
            $modelName .= " model";
        // model présent mais attaché
        else
            $modelName = substr(string: $modelName,offset: 0,length: -strlen(string: "model") ) . " model";

        $modelName = self::formatNameForClass(baseName: $modelName);

        // récupération des données de la class parent
        $searchStartDirPath = APP_CONFIG->getConfig(name: "ROOT") . "/src/models";

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
            "model-description" => $description,
            "parent-class" => $parentClass,
            "model-import-config" => $namespace !== null ? "namespace $namespace;" : "",
            "model-name" => $modelName,
            "represented-table" => $tableName
        ];
        $destination = "$parentClassDirPath/$modelName.php";

        // création du model
        if(self::createFromTemplate(templatePath: "/model-template.txt",dstPath: $destination,replacements: $replacements) ){
            Printer::printStyle(
                toPrint: "Model <$modelName> crée dans <$destination>",
                compositeStyle: $themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value)
            );
        }
        else{
            Printer::printStyle(
                toPrint: "Echec de création du model, veuillez retenter",
                compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
            );
        }
    }

    #[Override]
    public function getHelpLines():array{
        return [
            "Crée un model",
            "php sabo $this->commandName --name={nom du model}",
            "Options requises :",
            "\t--name : Nom du model",
            "\t--description : Description du model",
            "\t--table : Nom de la table représentée par le model en base de données",
            "Options optionnelles :",
            "\t--parent : Nom du fichier contenant la class dont va extends le model - par défaut le model extends de CustomModel. Recherche à partir du dossier src/models"
        ];
    }
}