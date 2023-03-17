<?php

namespace Sabo\DefaultPage;

/**
 * interface définissant un affichable
 */
interface Showable{
    /**
     * affiche l'élement
     */
    public function show():void;
}