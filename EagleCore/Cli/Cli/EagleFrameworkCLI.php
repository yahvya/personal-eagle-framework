<?php

namespace Yahvya\EagleFramework\Cli\Cli;

use BeBat\ConsoleColor\Style\Color;
use Yahvya\EagleFramework\Cli\Commands\EagleFrameworkCLICommand;
use Yahvya\EagleFramework\Cli\Theme\Theme;
use Yahvya\EagleFramework\Config\Config;
use Yahvya\EagleFramework\Utils\Printer\Printer;
use Throwable;

/**
 * @brief Eagle Framework CLI handler
 */
class EagleFrameworkCLI
{
    /**
     * @var ArgumentManager Argument manager instance
     */
    protected(set) ArgumentManager $argumentManager;

    /**
     * @var Config Current CLI print theme configuration
     */
    protected(set) Config $themeConfig;

    /**
     * @var array<string,EagleFrameworkCLICommand> Registered commands
     */
    protected(set) array $commands = [];

    /**
     * @param string[] $argv CLI arguments based on $argv
     * @param Config $themeConfig CLI print theme configuration
     */
    public function __construct(array $argv, Config $themeConfig)
    {
        $this->argumentManager = new ArgumentManager(argv: $argv);
        $this->themeConfig = $themeConfig;
    }

    /**
     * @brief Register a new command
     * @param EagleFrameworkCLICommand $executor New command executor
     * @return $this
     */
    public function registerCommand(EagleFrameworkCLICommand $executor): EagleFrameworkCLI
    {
        $this->commands[$executor->commandName] = $executor;

        return $this;
    }

    /**
     * @brief Launch the CLI command treatment
     * @return void
     */
    public function start(): void
    {
        try
        {
            $command = $this->argumentManager->next();

            if ($command == null)
            {
                Printer::printStyle(
                    toPrint: "Please provide the command to launch",
                    compositeStyle: $this->themeConfig->getConfig(Theme::IMPORTANT_ERROR_STYLE->value)
                );
                return;
            }

            $commandName = $command->argumentValue;

            if (!array_key_exists(key: $commandName, array: $this->commands))
            {
                Printer::printStyle(
                    toPrint: "Command not found, think about the (help) command ;)",
                    compositeStyle: $this->themeConfig->getConfig(Theme::BASIC_ERROR_STYLE->value)
                );
                return;
            }

            $this->commands[$commandName]->execCommand(cli: $this);
        }
        catch (Throwable $e)
        {
            Printer::print(
                toPrint: "The command execution failed due to {$e->getMessage()}",
                textColor: Color::Red
            );
        }
    }
}