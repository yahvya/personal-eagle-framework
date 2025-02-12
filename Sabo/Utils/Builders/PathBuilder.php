<?php

namespace Sabo\Utils\Builders;

/**
 * Path builder
 */
abstract class PathBuilder
{
    /**
     * Build a path by joining the different parts of the path
     * @param string ...$parts Path parts
     * @return string|null Built path
     */
    public static function buildPath(string... $parts):?string
    {
        if(empty($parts))
            return null;

        $finalPart = array_pop(array: $parts);

        # remove / or \\ on each parts
        $partsWithoutSlash = array_map(
            callback: function($pathPart):string{
                if(str_starts_with(haystack: $pathPart,needle: "/") || str_starts_with(haystack: $pathPart,needle: "\\"))
                    $pathPart = substr(string: $pathPart,offset: 1);

                if(str_ends_with(haystack: $pathPart,needle: "/") || str_ends_with(haystack: $pathPart,needle: "\\"))
                    $pathPart = substr(string: $pathPart,offset: 0,length: strlen(string: $pathPart) - 1);

                return $pathPart;
            },
            array: $parts
        );

        return implode(separator: "/",array: $partsWithoutSlash) . $finalPart;
    }
}