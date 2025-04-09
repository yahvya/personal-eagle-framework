<?php

namespace Sabo\Utils\Routes;

use Closure;

/**
 * Delete route
 */
class DeleteRoute extends Route
{
    public function __construct(
        string $link,
        string $routeName,
        array|Closure $handler
    )
    {
        parent::__construct(
            link: $link,
            requestMethod: "DELETE",
            routeName: $routeName,
            handler: $handler
        );
    }
}