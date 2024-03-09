<?php

/**
 * @brief Fichier de configuration twig
 */

use SaboCore\Utils\TwigExtensions\DefaultExtensions;

/**
 * @brief Aide à la création d'extension https://symfony.com/doc/3.x/templating/twig_extension.html
 * @return string[] tableau des class d'extensions twig [CustomExtension::class]
 */
function registerTwigExtensions():array{
    return [DefaultExtensions::class];
}