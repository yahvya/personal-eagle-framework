<?php

namespace SaboCore\Cli\Cli;

/**
 * @brief Argument de ligne de commande
 * @author yahaya bathily https://github.com/yahvya
 */
class CliArgument{
    /**
     * @var string|null Option trouvée (--option=....) ou null si pas d'option
     */
    protected string|null $option;

    /**
     * @var string Contenu de l'argument sans l'option potentielle
     */
    protected string $argumentValue;

    /**
     * @var string Chaine complète de l'argument
     */
    protected string $argument;

    /**
     * @param string $argument Argument complet saisi en ligne de commande
     */
    public function __construct(string $argument){
        ["option" => $this->option,"argumentValue" => $this->argumentValue] = self::extractArgDatas(argument: $argument);
        $this->argument = $argument;
    }

    /**
     * @return string|null option trouvée (--option=....) ou null si pas d'option
     */
    public function getOption():?string{
        return $this->option;
    }

    /**
     * @return string contenu de l'argument sans l'option potentielle
     */
    public function getArgumentValue():string{
        return $this->argumentValue;
    }

    /**
     * @return string Chaine complète de l'argument
     */
    public function getArgument():string{
        return $this->argument;
    }

    /**
     * @brief Extrait l'option et la valeur à partir d'une chaine au format argument de ligne de commande
     * @param string $argument Argument complet
     * @return array les données extraites au format ["option" → ..., "argumentValue" → ...]
     */
    public static function extractArgDatas(string $argument):array{
        // récupération de l'option et de l'argument
        @preg_match(pattern: "#(--(.*)=)?(.*)#",subject: $argument,matches: $matches);

        if(empty($matches[2]) || empty($matches[3]) ){
            $option = null;
            $argumentValue = $matches[0];
        }
        else{
            $option = $matches[2];
            $argumentValue = $matches[3];
        }

        return ["option" => $option,"argumentValue" => $argumentValue];
    }
}
