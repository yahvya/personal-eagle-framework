<?php

namespace Sabo\Middleware\Middleware;

/**
 * représente une condition middleware à poser
 */
interface SaboMiddlewareCond{
    /**
     * vérifie la condition
     * @return bool si la condition à réussi
     */
    public static function verify():bool;

    /**
     * action à faire si la vérification échoue
     */
    public static function toDoOnFail():void;
}