<?php

namespace Sabo\Utils\Routes;

use Closure;

/**
 * Post route
 */
class PostRoute extends Route
{
    public function __construct(
        string $link,
        string $routeName,
        array|Closure $handler
    )
    {
        parent::__construct(
            link: $link,
            requestMethod: "POST",
            routeName: $routeName,
            handler: $handler
        );
    }
}