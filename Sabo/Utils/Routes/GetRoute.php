<?php

namespace Sabo\Utils\Routes;

use Closure;

/**
 * Get route
 */
class GetRoute extends Route
{
    public function __construct(
        string $link,
        string $routeName,
        array|Closure $handler
    )
    {
        parent::__construct(
            link: $link,
            requestMethod: "GET",
            routeName: $routeName,
            handler: $handler
        );
    }
}