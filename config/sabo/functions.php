<?php

// fonctions globales au programme

/**
 * affiche les données pre
 * alias var_dump
 * @datas les données à afficher
 */
function debug(mixed... $datas):void{
    echo "<pre>";
    var_dump(...$datas);
    die("</pre>");
}