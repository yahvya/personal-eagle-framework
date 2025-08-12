<?php

namespace Yahvya\EagleFramework\Cli\Commands;

use Override;
use Yahvya\EagleFramework\Cli\Cli\EagleFrameworkCLI;
use Yahvya\EagleFramework\Cli\Theme\Theme;
use Yahvya\EagleFramework\Utils\Printer\Printer;

/**
 * @brief Controller creation command
 */
class ControllerMakerCommand extends EagleFrameworkCLITemplateUserCommand
{
    /**
     * @const string Controller default description Description par défaut du controller
     */
    protected const string CONTROLLER_DEFAULT_DESCRIPTION = "Controller";

    #[Override]
    public function execCommand(EagleFrameworkCLI $cli): void
    {
        $argumentManager = $cli->argumentManager;
        $themeConfig = $cli->themeConfig;

        // Get the controller description
        $parentClass = $argumentManager->find(optionName: "parent")?->argumentValue ?? "CustomController";
        $controllerName = $this->getOptions($cli, "name")["name"];

        // Formatting the class name
        $lowerControllerName = strtolower(string: $controllerName);

        if (
            !str_ends_with(haystack: $controllerName, needle: "Controller") &&
            !str_ends_with(haystack: $lowerControllerName, needle: " controller") &&
            !str_ends_with(haystack: $controllerName, needle: "controller")
        )
            $controllerName .= " controller";
        // controller présent mais attaché
        else
            $controllerName = substr(string: $controllerName, offset: 0, length: -strlen(string: "controller")) . " controller";

        $controllerName = self::formatNameForClass(baseName: $controllerName);

        // Get parent class data
        $searchStartDirPath = APP_CONFIG->getConfig(name: "ROOT") . "/Src/Controllers";

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
            "controller-description" => $argumentManager->find(optionName: "description")?->argumentValue ?? self::CONTROLLER_DEFAULT_DESCRIPTION,
            "parent-class" => $parentClass,
            "controller-import-config" => $namespace !== null ? "namespace $namespace;" : "",
            "controller-name" => $controllerName
        ];
        $destination = "$parentClassDirPath/$controllerName.php";

        // Generation of the controller
        if (self::createFromTemplate(templatePath: "/controller-template.txt", dstPath: $destination, replacements: $replacements))
        {
            Printer::printStyle(
                toPrint: "Controller <$controllerName> created in <$destination>",
                compositeStyle: $themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value)
            );
        }
        else
        {
            Printer::printStyle(
                toPrint: "Fail to generate the controller, please retry",
                compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
            );
        }
    }

    #[Override]
    public function getHelpLines(): array
    {
        return [
            "Generate a controller class",
            "> php eagle $this->commandName --name={controller name}",
            "Required options :",
            "\t--name : Controller name",
            "Optional options :",
            "\t--description : Controller description - by default '" . self::CONTROLLER_DEFAULT_DESCRIPTION . "'",
            "\t--parent : File name which contain the class which the controller will extend - by default it will extend CustomController. Searched from Src/Controllers"
        ];
    }
}