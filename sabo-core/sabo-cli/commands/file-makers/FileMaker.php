<?php

namespace SaboCore\SaboCli\Commands\FileMakers;

/**
 * @brief file making functions
 */
trait FileMaker{
    /**
     * @brief models path
     */
    public const string MODELS_PATH = RESOURCES_PATH . "/models";

    /**
     * @brief create a file from a model
     * @param string $storageAbsolutePath path to store the result
     * @param string $modelPath model file path from the models directory in "resources"
     * @param array $replaces model replaces
     * @return bool creation success
     * @attention the model format is {generic}
     */
    protected function createFileFromModel(
        string $storageAbsolutePath,
        string $modelPath,
        array $replaces = []
    ):bool{
        $modelContent = @file_get_contents(filename: static::MODELS_PATH . $modelPath);

        if($modelContent === false){
            echo "> Echec de chargement du model <$modelPath>" . PHP_EOL;
            return false;
        }

        // format keys to generic format
        $searches = array_map(
            callback: fn(string $search):string => '{' . $search . '}',
            array: array_keys(array: $replaces)
        );

        $finalContent = str_replace(
            search: $searches,
            replace: array_values(array: $replaces),
            subject: $modelContent
        );

        return @file_put_contents(filename: $storageAbsolutePath,data: $finalContent) !== false;
    }
}