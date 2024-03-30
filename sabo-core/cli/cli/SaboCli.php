<?php

namespace SaboCore\Cli\Cli;

use BeBat\ConsoleColor\Style\Color;
use SaboCore\Cli\Commands\SaboCommand;
use SaboCore\Cli\Theme\Theme;
use SaboCore\Config\Config;
use SaboCore\Utils\Printer\Printer;
use Throwable;

/**
 * @brief Gestionnaire de commande du framework
 * @author yahaya bathily https://github.com/yahvya/
 */
class SaboCli{
    /**
     * @var ArgumentManager gestionnaire d'arguments
     */
    protected ArgumentManager $argumentManager;

    /**
     * @var Config configuration de thème
     */
    protected Config $themeConfig;

    /**
     * @var array<string,SaboCommand> commandes
     */
    protected array $commands = [];

    /**
     * @param string[] $argv arguments de la ligne de commande
     * @param Config $themeConfig configuration de thème
     */
    public function __construct(array $argv,Config $themeConfig){
        $this->argumentManager = new ArgumentManager(argv: $argv);
        $this->themeConfig = $themeConfig;
    }

    /**
     * @brief Enregistre une commande
     * @param SaboCommand $executor class d'exécution de la commande
     * @return $this
     */
    public function registerCommand(SaboCommand $executor):SaboCli{
        $this->commands[$executor->getName()] = $executor;

        return $this;
    }

    /**
     * @return SaboCommand[] les commandes
     */
    public function getCommands():array{
        return $this->commands;
    }

    /**
     * @return Config la configuration du thème
     */
    public function getThemeConfig():Config{
        return $this->themeConfig;
    }

    /**
     * @return ArgumentManager le gestionnaire d'arguments
     */
    public function getArgumentManager():ArgumentManager{
        return $this->argumentManager;
    }

    /**
     * @brief Lance l'exécution du traitement cli
     * @return void
     */
    public function start():void{
        try{
            // recherche et exécution de la commande
            $command = $this->argumentManager->next();

            if($command == null){
                Printer::printStyle(
                    toPrint: "Veuillez saisir la commande à lancer",
                    compositeStyle: $this->themeConfig->getConfig(Theme::IMPORTANT_ERROR_STYLE->value)
                );
                return;
            }

            $commandName = $command->getArgumentValue();

            if(!array_key_exists(key: $commandName,array: $this->commands) ){
                Printer::printStyle(
                    "Commande non trouvé, pensez à utilisez (help)",
                    $this->themeConfig->getConfig(Theme::BASIC_ERROR_STYLE->value)
                );
                return;
            }

            // exécution de la commande
            $this->commands[$commandName]->execCommand(cli: $this);
        }
        catch(Throwable){
            Printer::print(toPrint: "Echec d'exécution de la commande",textColor: Color::Red);
        }
    }
}