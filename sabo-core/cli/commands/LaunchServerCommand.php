<?php

namespace SaboCore\Cli\Commands;

use Override;
use SaboCore\Cli\Cli\SaboCli;
use SaboCore\Cli\Theme\Theme;
use SaboCore\Utils\Printer\Printer;

/**
 * @brief Commande de lancement de serveur
 * @author yahaya bathily https://github.com/yahvya
 */
class LaunchServerCommand extends SaboCommand{
    /**
     * @brief Port par défaut
     */
    protected const string DEFAULT_PORT = "8080";

    #[Override]
    public function execCommand(SaboCli $cli): void{
        $port = $cli->getArgumentManager()->next() ?? self::DEFAULT_PORT;
        $link = "127.0.0.1:$port";
        $rooter = ROOT . "/sabo-core/index.php";

        Printer::printStyle(
            "Lancement du serveur ($link)",
            $cli->getThemeConfig()->getConfig(Theme::SPECIAL_TEXT_STYLE->value),
            1
        );

        system("php -S $link $rooter");
    }

    #[Override]
    public function getHelpLines(): array{
        return [
            "Lance le serveur de développement - Port par défaut (" . self::DEFAULT_PORT . ")",
            "php sabo $this->commandName",
            "php sabo $this->commandName {port}",
        ];
    }
}