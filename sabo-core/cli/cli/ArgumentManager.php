<?php

namespace SaboCore\Cli\Cli;

/**
 * @brief Gestionnaire d'arguments de la ligne de commandes
 * @author yahaya bathily https://github.com/yahvya/
 */
class ArgumentManager{
    /**
     * @var CliArgument[] arguments de la ligne de commandes
     */
    protected array $args = [];

    /**
     * @var int index actuel de lecture
     */
    protected int $currentIndex = 0;

    /**
     * @param string[] $argv variable argv
     */
    public function __construct(array $argv){
        // conversion des arguments
        $this->args = array_map(
            callback: fn(string $arg):CliArgument => new CliArgument(argument: $arg),
            array: array_slice(array: $argv,offset: 1)
        );
    }

    /**
     * @brief Recherche l'argument précédent à consumer et place le curseur sur le précédent
     * @attention quand l'argument n'a pas été trouvé le curseur ne bouge pas
     * @return CliArgument|null l'argument s'il est trouvé ou null
     */
    public function previous():?CliArgument{
        return array_key_exists(key: $this->currentIndex - 1, array: $this->args) ? $this->args[--$this->currentIndex] : null;
    }

    /**
     * @brief Recherche l'argument actuel à consumer et place le curseur sur le suivant
     * @attention quand l'argument n'a pas été trouvé le curseur ne bouge pas
     * @return CliArgument|null l'argument s'il est trouvé ou null
     */
    public function next():?CliArgument{
        return array_key_exists(key: $this->currentIndex,array: $this->args) ? $this->args[$this->currentIndex++] : null;
    }

    /**
     * @brief Recherche un argument à partir du nom de l'option fourni
     * @param string $optionName Nom de l'option (comparaison sensible à la casse)
     * @param bool $fromCurrentIndex Si true alors la recherche débute à l'index interne laissé suite à l'utilisation de (previous / next)
     * @return CliArgument|null L'argument trouvé ou null si aucun trouvé
     */
    public function find(string $optionName,bool $fromCurrentIndex = false):CliArgument|null{
        $searchIndex = $fromCurrentIndex ? $this->currentIndex : 0;
        $countOfElements = count(value: $this->args);

        // recherche de l'option
        for(;$searchIndex < $countOfElements; $searchIndex++){
            $option = $this->args[$searchIndex]->getOption();

            if($option !== null && strcmp(string1: $option,string2: $optionName) === 0)
                return $this->args[$searchIndex];
        }

        return null;
    }

    /**
     * @return int le nombre d'arguments de la ligne de commande
     */
    public function getCount():int{
        return count(value: $this->args);
    }

    /**
     * @return CliArgument[] les arguments de la ligne de commande
     */
    public function getArgs():array{
        return $this->args;
    }
}
