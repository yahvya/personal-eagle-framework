<?php

/**
 * @brief Fonctions globales utilitaires du framework
 */

/**
 * @brief Débug les variables fournies
 * @param mixed ...$toDebug variables à débugger
 * @return void
 */
function debug(mixed ...$toDebug):void{
    dump(...$toDebug);
}

/**
 * @brief Débug les variables fournies et quitte le programme
 * @param mixed ...$toDebug variables à débugger
 */
function debugDie(mixed ...$toDebug):never{
    debug(...$toDebug);
    die();
}