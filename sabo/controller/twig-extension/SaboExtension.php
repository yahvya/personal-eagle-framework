<?php

namespace Sabo\Controller\TwigExtension;

use Twig\Extension\AbstractExtension;

/**
 * parent des extensions du framework
 */
abstract class SaboExtension extends AbstractExtension{
    /**
     * liste des class d'extensions du framework
     */
    private static array $extensions = [
        SaboRouteExtension::class
    ];

    /**
     * fonction visant à initialiser les ressources nécéssaires à l'extension peut être vide
     */
    abstract public static function initExtension();

    /**
     * @return array les extensions crée par sabo
     */
    public static function getExtensions():array{
        return self::$extensions;
    }
}