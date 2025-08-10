<?php

namespace Yahvya\EagleFramework\Cli\Cli;

/**
 * @brief CLI arguments manager
 */
class ArgumentManager
{
    /**
     * @var CliArgument[] Specified arguments in the CLI
     */
    protected(set) array $args = [];

    /**
     * @var int Current read index
     */
    protected int $currentIndex = 0;

    /**
     * @param string[] $argv CLI arguments based on $argv
     */
    public function __construct(array $argv)
    {
        $this->args = array_map(
            callback: fn(string $arg): CliArgument => new CliArgument(argument: $arg),
            array: array_slice(array: $argv, offset: 1)
        );
    }

    /**
     * @brief Find the previous arg to consume and put the cursor on
     * @attention If the argument isn't found, the cursor won't change
     * @return CliArgument|null The founded arg or null
     */
    public function previous(): ?CliArgument
    {
        return array_key_exists(key: $this->currentIndex - 1, array: $this->args) ? $this->args[--$this->currentIndex] : null;
    }

    /**
     * @brief Find the arg to consume (current index) and put the cursor on the next one
     * @attention If the argument isn't found, the cursor won't change
     * @return CliArgument|null The founded arg or null
     */
    public function next(): ?CliArgument
    {
        return array_key_exists(key: $this->currentIndex, array: $this->args) ? $this->args[$this->currentIndex++] : null;
    }

    /**
     * @brief Find an argument based on the provided option's name
     * @param string $optionName Option's name (case sensible comparison)
     * @param bool $fromCurrentIndex If true, the search began with the internal index or from 0 if false
     * @return CliArgument|null The founded arg or null
     */
    public function find(string $optionName, bool $fromCurrentIndex = false): CliArgument|null
    {
        $searchIndex = $fromCurrentIndex ? $this->currentIndex : 0;
        $countOfElements = count(value: $this->args);

        for (; $searchIndex < $countOfElements; $searchIndex++)
        {
            $option = $this->args[$searchIndex]->option;

            if ($option !== null && strcmp(string1: $option, string2: $optionName) === 0)
                return $this->args[$searchIndex];
        }

        return null;
    }

    /**
     * @return int The number of arguments passed to the CLI
     */
    public function getCount(): int
    {
        return count(value: $this->args);
    }
}
