<?php

namespace Yahvya\EagleFramework\Utils\TwigExtensions;

use Yahvya\EagleFramework\Utils\Csrf\CsrfManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @brief Framework twig default extensions
 */
class DefaultExtensions extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(name: "route", callable: [$this, "route"]),
            new TwigFunction(name: "generateCsrf", callable: [$this, "generateCsrf"]),
            new TwigFunction(name: "checkCsrf", callable: [$this, "checkCsrf"]),
        ];
    }

    use
        TwigRouteExtensionImpl,
        TwigCsrfExtensionImpl;
}