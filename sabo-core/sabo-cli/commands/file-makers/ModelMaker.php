<?php

namespace SaboCore\SaboCli\Commands\FileMakers;

use Override;
use SaboCore\SaboCli\ArgsParser\Parser;
use SaboCore\SaboCli\Commands\Commands\SaboCliCommand;

/**
 * @brief models maker
 */
class ModelMaker extends SaboCliCommand{
    use FileMaker;

    /**
     * @brief models storage path
     */
    protected const string MODEL_PATH = APP_ROOT . "/src/models";

    #[Override]
    public function executeCommand(Parser $parser): bool{
        echo "> Enter the model name : ";
        $modelName = trim(string: @fgets(stream: STDIN));

        if(
            $parser->optionExist(option: "description") ||
            $parser->optionExist(option: "D")
        )
            $modelDescription = $parser->getOptionValue(option: "description") ?? $parser->getOptionValue(option: "D");
        else
            $modelDescription = $modelName;

        if(
            $parser->optionExist(option: "author") ||
            $parser->optionExist(option: "A")
        )
            $modelAuthor = "\n * @author " . ($parser->getOptionValue(option: "author") ?? $parser->getOptionValue(option: "A"));
        else
            $modelAuthor = "";

        return $this->createFileFromModel(
            storageAbsolutePath: static::MODEL_PATH . "/$modelName.php",
            modelPath: "/model-model.txt",
            replaces: [
                "modelDescription" => $modelDescription,
                "author" => $modelAuthor,
                "modelName" => $modelName
            ]
        );
    }
}