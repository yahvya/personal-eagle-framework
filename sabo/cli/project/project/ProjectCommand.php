<?php

namespace Sabo\Cli\Project\Project;

/**
 * commande du manager
 */
interface ProjectManagerCommand{
    /**
     * exécute la commande
     * @param argc le nombre de commandes
     * @param argv les arguments passés
     */
    public function exec(int $argc,array $argv):bool;
}