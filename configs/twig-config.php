<?php

/**
 * @brief Twig configuration file
 */

use Yahvya\EagleFramework\Utils\TwigExtensions\DefaultExtensions;

/**
 * @brief Extension development help https://symfony.com/doc/3.x/templating/twig_extension.html
 * @return string[] Developed extensions classes [CustomExtension::class]
 */
function registerTwigExtensions(): array
{
    return [DefaultExtensions::class];
}