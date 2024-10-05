<?php

namespace SaboCore\SaboCli\ArgsParser;

/**
 * @brief arguments parser
 */
class Parser{
    /**
     * @var array{string:string} commands map
     */
    protected array $commandConfig;

    /**
     * @param string[] $args arguments
     */
    public function __construct(
        protected array $args
    ){
        $this->commandConfig = [];
        $this->parseArgs();
    }

    /**
     * @return bool there is a command founded
     */
    public function thereIsCommand():bool{
        return array_key_exists(key: "commandName",array: $this->commandConfig);
    }

    /**
     * @return string|null founded command name or null
     */
    public function getCommandName():?string{
        return $this->commandConfig["commandName"] ?? null;
    }

    /**
     * @return array{string:string} options map , indexed by option name , linked to the option value
     * @attention if the value is true as boolean it means there is no associated value to the option
     */
    public function getOptions():array{
        return $this->commandConfig["options"] ?? [];
    }

    /**
     * @param string $option option name
     * @return string|bool|null the option linked value or null if the option is not found
     * @attention if the value is true as boolean it means there is no associated value to the option
     *
     */
    public function getOptionValue(string $option):string|bool|null{
        return $this->commandConfig["options"][$option] ?? null;
    }

    /**
     * @param string $option option name
     * @return bool if the option exist
     */
    public function optionExist(string $option):bool{
        return array_key_exists(key: $option,array: $this->commandConfig["options"]);
    }

    /**
     * @brief parse the cli arguments
     * @return $this
     */
    protected function parseArgs():static{
        array_shift(array: $this->args);

        if(empty($this->args))
            return $this;

        $this->commandConfig = [
            "commandName" => array_shift(array: $this->args),
            "options" => []
        ];

        $countOfArgs = count(value: $this->args);
        $caseRegexes = [
            "isOption" => "#^--?#",
            "isSingleOption" => "#^--?(\S+)$#",
            "isOptionWithEqualSyntax" => "#^--?(\S+)=(.+)$#"
        ];

        for($index = 0; $index < $countOfArgs;$index++){
            $current = $this->args[$index];
            $next = $this->args[$index + 1] ?? null;

            if(!@preg_match(pattern: $caseRegexes["isOption"],subject: $current))
                continue;

            if(@preg_match(pattern: $caseRegexes["isOptionWithEqualSyntax"],subject: $current,matches: $matches)){
                $this->commandConfig["options"][$matches[1]] = $matches[2];
                continue;
            }

            if(@preg_match(pattern: $caseRegexes["isSingleOption"],subject: $current,matches: $matches)){
                if($next === null){
                    $this->commandConfig["options"][$matches[1]] = true;
                    continue;
                }

                if(!@preg_match(pattern: $caseRegexes["isOption"],subject: $next)){
                    $this->commandConfig["options"][$matches[1]] = $next;
                    $index++;
                    continue;
                }

                $this->commandConfig["options"][$matches[1]] = true;
            }
        }

        return $this;
    }
}