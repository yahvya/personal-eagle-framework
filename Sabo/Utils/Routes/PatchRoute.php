<?php

namespace Sabo\Utils\Routes;

use Closure;

/**
 * Patch route
 */
class PatchRoute extends Route
{
    public function __construct(
        string $link,
        string $routeName,
        array|Closure $handler
    )
    {
        parent::__construct(
            link: $link,
            requestMethod: "PATCH",
            routeName: $routeName,
            handler: $handler
        );
    }
}