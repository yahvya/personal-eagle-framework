<?php

namespace Yahvya\EagleFramework\Cli\Cli;

/**
 * @brief Representation of a CLI argument
 */
class CliArgument
{
    /**
     * @var string|null Founded option (--option=...) or null if no option
     */
    protected(set) string|null $option;

    /**
     * @var string The argument content without the potential option string
     */
    protected(set) string $argumentValue;

    /**
     * @var string Complete string of the argument
     */
    protected(set) string $argument;

    /**
     * @param string $argument Full CLI argument string
     */
    public function __construct(string $argument)
    {
        ["option" => $this->option, "argumentValue" => $this->argumentValue] = self::extractArgDatas(argument: $argument);
        $this->argument = $argument;
    }

    /**
     * @brief Extract the option, and it's value based on the provided argument string
     * @param string $argument CLI argument string
     * @return array{option:null|string,argumentValue:string} The extracted data in the described format
     */
    public static function extractArgDatas(string $argument): array
    {
        // Extraction of the option name and the associated value
        @preg_match(pattern: "#(--(.*)=)?(.*)#", subject: $argument, matches: $matches);

        if (empty($matches[2]) || empty($matches[3]))
        {
            $option = null;
            $argumentValue = $matches[0];
        }
        else
        {
            $option = $matches[2];
            $argumentValue = $matches[3];
        }

        return ["option" => $option, "argumentValue" => $argumentValue];
    }
}
