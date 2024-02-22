<?php

// fonctions globales au programme

/**
 * affiche les données pre
 * alias var_dump
 * @datas les données à afficher
 */
function debug(mixed... $datas):never{
    echo "<pre>";
    dump(...$datas);
    die("</pre>");
}