<?php

namespace SaboCore\SaboCli\Commands\FileMakers;

use Override;
use SaboCore\SaboCli\ArgsParser\Parser;
use SaboCore\SaboCli\Commands\Commands\SaboCliCommand;

/**
 * @brief controller maker
 */
class ControllerMaker extends SaboCliCommand{
    use FileMaker;

    /**
     * @brief controllers storage path
     */
    protected const string CONTROLLERS_PATH = APP_ROOT . "/src/controllers";

    #[Override]
    public function executeCommand(Parser $parser): bool{
        echo "> Enter the controller name : ";
        $controllerName = trim(string: @fgets(stream: STDIN));

        if(
            $parser->optionExist(option: "description") ||
            $parser->optionExist(option: "D")
        )
            $controllerDescription = $parser->getOptionValue(option: "description") ?? $parser->getOptionValue(option: "D");
        else
            $controllerDescription = $controllerName;

        if(
            $parser->optionExist(option: "author") ||
            $parser->optionExist(option: "A")
        )
            $controllerAuthor = "\n * @author " . ($parser->getOptionValue(option: "author") ?? $parser->getOptionValue(option: "A"));
        else
            $controllerAuthor = "";

        return $this->createFileFromModel(
            storageAbsolutePath: static::CONTROLLERS_PATH . "/$controllerName.php",
            modelPath: "/controller-model.txt",
            replaces: [
                "controllerDescription" => $controllerDescription,
                "author" => $controllerAuthor,
                "controllerName" => $controllerName
            ]
        );
    }
}