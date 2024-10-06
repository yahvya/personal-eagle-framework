<?php

namespace SaboCore\SaboCli\Commands\Commands;

use SaboCore\SaboCli\ArgsParser\Parser;
use SimpleXMLElement;
use Throwable;

/**
 * @brief cli manager
 */
abstract class SaboCliCommand{
    /**
     * @var array{string:array} commands configuration
     */
    protected static array $commands = [];

    public function __construct(){}

    /**
     * @brief execute the command
     * @param Parser $parser parser
     * @return bool execution success
     */
    public abstract function executeCommand(Parser $parser):bool;

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
     * @brief treat the arguments to launch commands
     * @param string[] $args arguments
     * @return bool treatment success
     */
    public static function treat(array $args):bool{
        $parser = new Parser(args: $args);

        if(!$parser->thereIsCommand()){
            echo "> Please provide a command to execute";
            self::printCommandsList(commands: self::$commands);
            return false;
        }

        $commandName = $parser->getCommandName();

        if(!array_key_exists(key: $commandName,array: self::$commands)){
            echo "> Command not found";
            self::printCommandsList(commands: self::$commands);
            return false;
        }

        $executorClass = self::$commands[$commandName]["class"];
        $executor = new $executorClass();

        $executor->executeCommand(parser: $parser);

        return true;
    }

    /**
     * @brief print command list
     * @param array $commands commands
     * @return void
     */
    public static function printCommandsList(array $commands):void{
        foreach($commands as $commandName => $commandConfig) {
echo "

-------------------------------------------------------------------------
[$commandName]

> {$commandConfig["description"]}";

            if(!empty($commandConfig["options"])){
echo "

Options :";

                foreach($commandConfig["options"] as $optionConfig){
                    $names = implode(separator: ",",array: $optionConfig["names"]);
                    $isRequired = $optionConfig["isRequired"] ? "Oui" : "Non";
echo "

  $names
      > {$optionConfig["description"]}
      Requis : $isRequired
      Par défaut : {$optionConfig["default"]}";

                    if(!empty($optionConfig["requirements"])){
echo "

      Requis:";
                        foreach($optionConfig["requirements"] as $requirementConfig){
echo "
          {$requirementConfig["name"]} ({$requirementConfig["type"]})
            > {$requirementConfig["description"]}";
                        }
                    }
                }
            }

echo "
-------------------------------------------------------------------------";
        }
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

            if(!empty($option->requirements->requirement)){
                foreach($option->requirements->requirement as $requirement){
                    $requirementDatas = [
                        "type" => $requirement->type->__toString(),
                        "name" => $requirement->name->__toString(),
                        "description" => $requirement->description->__toString()
                    ];

                    if($requirementDatas["type"] === RequirementTypes::COMMAND->value)
                        $requirementDatas["commandInstall"] = $requirement->command_install->__toString();

                    $optionDatas["requirements"][] = $requirementDatas;
                }
            }

            $command["options"][] = $optionDatas;
        }

        return $command;
    }
}