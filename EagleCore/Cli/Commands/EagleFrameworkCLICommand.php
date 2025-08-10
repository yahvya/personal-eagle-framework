<?php

namespace Yahvya\EagleFramework\Cli\Commands;

use Yahvya\EagleFramework\Cli\Cli\EagleFrameworkCLI;
use Yahvya\EagleFramework\Cli\Theme\Theme;
use Yahvya\EagleFramework\Config\Config;
use Yahvya\EagleFramework\Config\ConfigException;
use Yahvya\EagleFramework\Utils\Printer\Printer;
use Throwable;

/**
 * @brief Abstract command
 */
abstract class EagleFrameworkCLICommand
{

    /**
     * @param string $commandName Command name
     */
    public function __construct(
        protected(set) string $commandName
    )
    {
    }

    /**
     * @brief Provided the values of the searched options
     * @param EagleFrameworkCLI $cli Cli
     * @param string ...$optionNames Options names
     * @return array{string:string} Options iu the format ["nom option" → "valuer option"]
     * @attention To use for the required options
     * @throws ConfigException On error
     */
    protected function getOptions(EagleFrameworkCLI $cli, string ...$optionNames): array
    {
        $result = [];
        $argumentManager = $cli->argumentManager;
        $themeConfig = $cli->themeConfig;

        foreach ($optionNames as $optionName)
        {
            $result[$optionName] =
                $argumentManager->find(optionName: $optionName)?->argumentValue ??

                $this->ask(toAsk: "Please provide a value for the option <$optionName>", themeConfig: $themeConfig);
        }

        return $result;
    }

    /**
     * @brief Ask a question
     * @param string $toAsk The question
     * @param Config $themeConfig Theme configuration
     * @return string User input
     * @throws ConfigException On error
     */
    protected function ask(string $toAsk, Config $themeConfig): string
    {
        Printer::printStyle(toPrint: "> $toAsk : ", compositeStyle: $themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value));
        return trim(string: fgets(stream: STDIN));
    }

    /**
     * @brief Execute the command
     * @param EagleFrameworkCLI $cli The associated CLI manager instance
     * @return void
     * @throws ConfigException|Throwable On error
     */
    public abstract function execCommand(EagleFrameworkCLI $cli): void;

    /**
     * @return string[] Help lines to print
     */
    public abstract function getHelpLines(): array;
}