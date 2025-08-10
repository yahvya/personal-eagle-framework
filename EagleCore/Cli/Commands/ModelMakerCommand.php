<?php

namespace Yahvya\EagleFramework\Cli\Commands;

use Override;
use Yahvya\EagleFramework\Cli\Cli\EagleFrameworkCLI;
use Yahvya\EagleFramework\Cli\Theme\Theme;
use Yahvya\EagleFramework\Utils\Printer\Printer;

/**
 * @brief Model class creation command
 */
class ModelMakerCommand extends EagleFrameworkCLITemplateUserCommand
{
    #[Override]
    public function execCommand(EagleFrameworkCLI $cli): void
    {
        $argumentManager = $cli->argumentManager;
        $themeConfig = $cli->themeConfig;

        // récupération de la configuration descriptive du model
        $parentClass = $argumentManager->find(optionName: "parent")?->argumentValue ?? "CustomModel";
        [
            "name" => $modelName,
            "description" => $description,
            "table" => $tableName
        ] = $this->getOptions($cli, "name", "description", "table");

        // Formating model name
        $lowerModelName = strtolower(string: $modelName);

        if (
            !str_ends_with(haystack: $modelName, needle: "Model")
            &&
            !str_ends_with(haystack: $lowerModelName, needle: " model") &&
            !str_ends_with(haystack: $modelName, needle: "model")
        )
            $modelName .= " model";
        else
            $modelName = substr(string: $modelName, offset: 0, length: -strlen(string: "model")) . " model";

        $modelName = self::formatNameForClass(baseName: $modelName);

        $searchStartDirPath = APP_CONFIG->getConfig(name: "ROOT") . "/Src/Models";

        $parentClassConfig = self::findClassDatas(
            className: $parentClass,
            from: $searchStartDirPath
        );

        if ($parentClassConfig === null)
        {
            Printer::printStyle(
                toPrint: "Parent class data didn't found in one of these (dir|sub) directories of <$searchStartDirPath>",
                compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
            );
            return;
        }

        ["namespace" => $namespace, "directory" => $parentClassDirPath] = $parentClassConfig;

        $replacements = [
            "model-description" => $description,
            "parent-class" => $parentClass,
            "model-import-config" => $namespace !== null ? "namespace $namespace;" : "",
            "model-name" => $modelName,
            "represented-table" => $tableName
        ];
        $destination = "$parentClassDirPath/$modelName.php";

        if (self::createFromTemplate(templatePath: "/model-template.txt", dstPath: $destination, replacements: $replacements))
        {
            Printer::printStyle(
                toPrint: "Model <$modelName> crée dans <$destination>",
                compositeStyle: $themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value)
            );
        } else
        {
            Printer::printStyle(
                toPrint: "Fail to generate the model, please retry",
                compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
            );
        }
    }

    #[Override]
    public function getHelpLines(): array
    {
        return [
            "Generate a model class",
            "> php sabo $this->commandName --name={model name}",
            "Required options :",
            "\t--name : Model name",
            "\t--description : Model description",
            "\t--table : Associated table name",
            "Optional options :",
            "\t--parent : File name which contain the class which the model will extend - by default it will extend CustomModel. Searched from Src/Models"
        ];
    }
}