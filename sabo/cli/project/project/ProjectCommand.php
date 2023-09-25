<?php

namespace Sabo\Cli\Project\Project;

/**
 * commande du manager
 */
interface ProjectManagerCommand{
    /**
     * exécute la commande
     * @param int $argc le nombre de commandes
     * @param array $argv les arguments passés
     * @return bool si l'exécution de la commande réussi
     */
    public function exec(int $argc,array $argv):bool;
}