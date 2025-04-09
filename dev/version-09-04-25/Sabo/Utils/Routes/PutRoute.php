<?php

namespace Sabo\Utils\Routes;

use Closure;

/**
 * Put route
 */
class PutRoute extends Route
{
    public function __construct(
        string $link,
        string $routeName,
        array|Closure $handler
    )
    {
        parent::__construct(
            link: $link,
            requestMethod: "PUT",
            routeName: $routeName,
            handler: $handler
        );
    }
}