<?php

namespace SaboCore\Cli\Commands;

use SaboCore\Cli\Cli\SaboCli;
use Override;

/**
 * @brief Commande de lancement du serveur de développement
 * @author yahaya bathily https://github.com/yahvya/
 */
class LaunchServerCommand extends SaboCommand{
    #[Override]
    public function execCommand(SaboCli $cli): void{
        $defaultPort = "8080";
        $port = $this->ask("Veuillez saisir le port à utiliser (ou entrée pour $defaultPort)",$cli->getThemeConfig());

        if(empty($port) ) $port = $defaultPort;

        system("php -S 127.0.0.1:$port -t " . ROOT . "/public");
    }

    #[Override]
    public function getHelpLines():array{
        return [
            "php sabo $this->commandName",
            "Lance le serveur de développement"
        ];
    }
}