<?php

namespace SaboCore\SaboCli\Commands\Commands;

use SimpleXMLElement;
use Throwable;

abstract class SaboCliCommand{
    /**
     * @var array{string:array} commands configuration
     */
    protected static array $commands = [];

    /**
     * @brief load the commands descriptions and store them
     * @param string $descriptionFileAbsolutePath command description xml file absolute path
     * @return bool if the loading succeed
     */
    public static function loadCommandsDescriptions(string $descriptionFileAbsolutePath):bool{
        try{
            $xmlData = simplexml_load_file(filename: $descriptionFileAbsolutePath);

            foreach($xmlData->command as $command) {
                $commandName = $command->name->__toString();

                if (array_key_exists(key: $commandName, array: self::$commands))
                    return false;

                self::$commands[$commandName] = self::parseCommand(xmlCommand: $command);
            }
        }
        catch(Throwable){
            return false;
        }

        return true;
    }

    /**
     * @brief convert the xml format to a formated array
     * @param SimpleXMLElement $xmlCommand command
     * @return array formated array
     */
    protected static function parseCommand(SimpleXMLElement $xmlCommand):array{
        $command = [
            "name" => $xmlCommand->name->__toString(),
            "description" => $xmlCommand->description->__toString(),
            "class" => $xmlCommand->class->__toString(),
            "options" => []
        ];

        foreach($xmlCommand->options->option as $option){
            $optionDatas = [
                "names" => [],
                "requirements" => [],
                "isRequired" => $option->is_required->__toString() === "true",
                "description" => $option->description->__toString(),
                "default" => $option->default->__toString()
            ];

            if(empty($optionDatas["default"]))
                $optionDatas["default"] = null;

            foreach($option->names->name as $name)
                $optionDatas["names"][] = $name->__toString();

            foreach($option->requirements->requirement as $requirement){
                $requirementDatas = [
                    "type" => $requirement->type->__toString(),
                    "name" => $requirement->name->__toString(),
                    "description" => $requirement->description->__toString()
                ];

                if($requirementDatas["type"] === RequirementTypes::COMMAND->value)
                    $requirementDatas["commandInstall"] = $requirement->command_install->__toString();

                $command["requirements"][] = $requirementDatas;
            }

            $command["options"][] = $optionDatas;
        }

        return $command;
    }
}