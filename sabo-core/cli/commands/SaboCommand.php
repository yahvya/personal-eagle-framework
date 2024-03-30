<?php

namespace SaboCore\Cli\Commands;

use SaboCore\Cli\Cli\ArgumentManager;
use SaboCore\Cli\Cli\SaboCli;
use SaboCore\Cli\Theme\Theme;
use SaboCore\Config\Config;
use SaboCore\Config\ConfigException;
use SaboCore\Utils\Printer\Printer;
use Throwable;

/**
 * @brief Commande
 * @author yahaya bathily https://github.com/yahvya/
 */
abstract class SaboCommand{
    /**
     * @var string nom de la commande
     */
    protected string $commandName;

    /**
     * @param string $commandName nom de la commande
     */
    public function __construct(string $commandName){
        $this->commandName = $commandName;
    }

    /**
     * @return string le nom de la commande
     */
    public function getName():string{
        return $this->commandName;
    }

    /**
     * @brief Récupère les valeurs des options recherchées ou les demandes si non trouvées
     * @attention à utiliser pour les options obligatoires
     * @param SaboCli $cli Cli
     * @param string ...$optionNames nom des options recherchées
     * @return array Les options au format ["nom option" → "valuer option"]
     * @throws ConfigException en cas d'erreur
     */
    protected function getOptions(SaboCli $cli,string ...$optionNames):array{
        $result = [];
        $argumentManager = $cli->getArgumentManager();
        $themeConfig = $cli->getThemeConfig();

        foreach($optionNames as $optionName){
            $result[$optionName] =
                // recherche de l'option parmi les arguments
                $argumentManager->find(optionName: $optionName)?->getArgumentValue() ??
                // demande de la valeur de l'option
                $this->ask(toAsk: "Veuillez fournir une valeur pour l'option <$optionName>",themeConfig: $themeConfig);
        }

        return $result;
    }

    /**
     * @brief Pose une question
     * @param string $toAsk la question
     * @param Config $themeConfig configuration de thème
     * @return string la réponse saisie
     * @throws ConfigException en cas d'erreur de thème
     */
    protected function ask(string $toAsk, Config $themeConfig):string{
        Printer::printStyle(toPrint: "> $toAsk : ",compositeStyle: $themeConfig->getConfig(name: Theme::SPECIAL_TEXT_STYLE->value) );
        return trim(string: fgets(stream: STDIN));
    }

    /**
     * @brief Exécute la commande
     * @param SaboCli $cli le gestionnaire cli lié
     * @throws ConfigException|Throwable en cas d'erreur interne
     * @return void
     */
    public abstract function execCommand(SaboCli $cli):void;

    /**
     * @return string[] les lignes à afficher pour l'aide de la commande
     */
    public abstract function getHelpLines():array;
}