<?php

namespace SaboCore\Cli\Cli;

/**
 * @brief Gestionnaire d'arguments de la ligne de commandes
 * @author yahaya bathily https://github.com/yahvya/
 */
class ArgumentManager{
    /**
     * @var string[] arguments de la ligne de commandes
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
        $this->args = array_slice($argv,1);
    }

    /**
     * @brief Recherche l'argument précédent à consumer et place le curseur sur le précédent
     * @attention quand l'argument n'a pas été trouvé le curseur ne bouge pas
     * @return string|null l'argument s'il est trouvé ou null
     */
    public function previous():?string{
        return array_key_exists($this->currentIndex - 1,$this->args) ? $this->args[--$this->currentIndex] : null;
    }

    /**
     * @brief Recherche l'argument actuel à consumer et place le curseur sur le suivant
     * @attention quand l'argument n'a pas été trouvé le curseur ne bouge pas
     * @return string|null l'argument s'il est trouvé ou null
     */
    public function next():?string{
        return array_key_exists($this->currentIndex,$this->args) ? $this->args[$this->currentIndex++] : null;
    }

    /**
     * @return int le nombre d'arguments de la ligne de commande
     */
    public function getCount():int{
        return count($this->args);
    }

    /**
     * @return string[] les arguments de la ligne de commande
     */
    public function getArgs():array{
        return $this->args;
    }
}