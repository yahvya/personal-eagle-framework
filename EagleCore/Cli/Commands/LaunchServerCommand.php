<?php

namespace Yahvya\EagleFramework\Cli\Commands;

use Override;
use Yahvya\EagleFramework\Cli\Cli\EagleFrameworkCLI;
use Yahvya\EagleFramework\Cli\Theme\Theme;
use Yahvya\EagleFramework\Config\ConfigException;
use Yahvya\EagleFramework\Utils\Printer\Printer;

/**
 * @brief Local server launch command
 */
class LaunchServerCommand extends EagleFrameworkCLICommand
{
    /**
     * @const string Default port
     */
    protected const string DEFAULT_PORT = "8080";

    /**
     * @const string Default host
     */
    protected const string DEFAULT_HOST = "127.0.0.1";

    /**
     * @const string Accepted separator on files
     */
    protected const string FILES_SEPARATOR = ",";

    /**
     * @const string Default extension of files which will be listened to
     */
    protected const array DEFAULT_FILE_TYPES = ["php", "js", "css", "twig", "blade"];

    /**
     * @const string Synchronization command
     */
    protected const string SYNC_COMMAND_NAME = "browser-sync";

    #[Override]
    public function execCommand(EagleFrameworkCLI $cli): void
    {
        $themeConfig = $cli->themeConfig;

        // récupération des options
        $argumentManager = $cli->argumentManager;

        $port = $argumentManager->find(optionName: "port")?->argumentValue ?? self::DEFAULT_PORT;
        $host = $argumentManager->find(optionName: "host")?->argumentValue ?? self::DEFAULT_HOST;
        $sync = $argumentManager->find(optionName: "sync");

        $link = "$host:$port";
        $rooter = APP_CONFIG->getConfig(name: "ROOT") . "/SaboCore/index.php";

        if ($sync !== null)
        {
            if (!self::manageSyncRequirements(cli: $cli))
            {
                Printer::printStyle(
                    toPrint: "Fail to treat the synchronisation command",
                    compositeStyle: $cli->themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
                );

                return;
            }

            $extensions = explode(separator: self::FILES_SEPARATOR, string: $sync->argumentValue);
            $extensions = implode(
                separator: ",",
                array: array_map(
                    callback: fn(string $extension): string => "**/*.$extension",
                    array: $extensions[0] === "default" ? self::DEFAULT_FILE_TYPES : $extensions
                )
            );

            $syncProcess = popen(command: self::SYNC_COMMAND_NAME . " start --proxy $link --files \"$extensions\"", mode: "r");

            if ($syncProcess === false)
            {
                Printer::printStyle(
                    toPrint: "Fail to launch the synchronization process",
                    compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
                );

                return;
            }
        }

        Printer::printStyle(
            toPrint: "Server launching ($link)",
            compositeStyle: $themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value),
            countOfLineBreak: 1
        );

        $serverProcess = @popen(command: "php -S $link $rooter", mode: "r");

        if ($serverProcess === false)
        {
            Printer::printStyle(
                toPrint: "Fail to launch the server",
                compositeStyle: $themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
            );

            return;
        }

        // Read sync process outputs
        while (true)
        {
            if (isset($syncProcess))
            {
                while (($syncLine = fgets(stream: $syncProcess)) !== false)
                    print($syncLine);
            }

            while (($processLine = fgets(stream: $serverProcess)) !== false)
                print($processLine);
        }
    }

    #[Override]
    public function getHelpLines(): array
    {
        return [
            "Launch the development server - Default port (" . self::DEFAULT_PORT . ") - Default host (" . self::DEFAULT_HOST . ")",
            "> php sabo $this->commandName",
            "Optional options :",
            "\t--port : Port number",
            "\t--host : Host address",
            "\t--sync: if this option is specified <browser-sync> will be used (a npm installation is required to use this command)",
            "\t\tYou can specify, as the value of the --sync option, the extensions of the files you want <browser-sync> to listen separated by <" . self::FILES_SEPARATOR . ">.",
            "\t\tUse <default> as the --sync option value if you want to listen to these default types: (" . implode(separator: ",", array: self::DEFAULT_FILE_TYPES) . ")."
        ];
    }

    /**
     * @brief Check if the sync command if installed or try to install it using npm
     * @param EagleFrameworkCLI $cli Cli
     * @return bool If the command is available to use
     * @throws ConfigException On error
     */
    public static function manageSyncRequirements(EagleFrameworkCLI $cli): bool
    {
        // Check if the command exist
        if (@exec(command: "npm list -g --depth=0 --parseable=true", output: $result) === false)
            return false;

        if (empty($result))
            return false;

        if (!empty(
        array_filter(
            array: $result,
            callback: fn(string $line): bool => str_contains(haystack: $line, needle: self::SYNC_COMMAND_NAME)
        )
        ))
            return true;

        $installationSuccess = @system(command: "npm install -g " . self::SYNC_COMMAND_NAME, result_code: $resultCode) === false || $resultCode !== 0;

        if ($installationSuccess)
        {
            Printer::printStyle(
                toPrint: "<" . self::SYNC_COMMAND_NAME . "> have been installed",
                compositeStyle: $cli->themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value)
            );

            return true;
        } else
        {
            Printer::printStyle(
                toPrint: "Fail to install the <" . self::SYNC_COMMAND_NAME . "> command",
                compositeStyle: $cli->themeConfig->getConfig(name: Theme::BASIC_ERROR_STYLE->value)
            );

            return false;
        }
    }
}
