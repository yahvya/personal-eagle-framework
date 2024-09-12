<?php

namespace SaboCore\Routing\Request;

use SaboCore\Utils\CustomTypes\Map;

/**
 * @brief uri matcher
 */
class UriMatcher{
    /**
     * @param string $baseUri base url
     */
    public function __construct(public string $baseUri){
    }

    /**
     * @brief match the pattern with the stored uri
     * @param string $pattern route pattern to match
     * @param string|null $genericParamsMatcherRegex a regular expression to match generic params: ex \:([a-zA-Z_]+) to match :element_name generic param format and capture the generic name
     * @param Map $genericParamsCustomRegex generic params linked regex
     * @param string $genericParamDefaultRegex the default regex to associate to each generic params
     * @return array|null null if not match or an array indexed with the variable name with the founded value in the request uri
     * @attention each generic params have to get a unique name
     */
    public function matchPattern(
        string  $pattern,
        ?string $genericParamsMatcherRegex = null,
        Map $genericParamsCustomRegex = new Map(),
        string $genericParamDefaultRegex = "[^/]+"
    ):?array{
        # format the pattern
        $pattern = $this->formatPattern(pattern: $pattern);

        # if there is nothing to match, check with a simple comparaison
        if($genericParamsMatcherRegex === null && $pattern === $this->baseUri)
            return [];

        # extract all generic params
        if(@preg_match_all(pattern: "#$genericParamsMatcherRegex#",subject: $pattern,matches: $matches) === false)
            return null;

        # array to link a generic params full pattern with the linked regex
        $genericParamsRegexAssociation = [];
        $genericParamsOrderedNames = [];

        if(!empty($matches[1])){
            // affect to all generic params the default regex and start to fill the result array keys
            foreach($matches[1] as $key => $genericParamName){
                $regex = $genericParamsCustomRegex->haveKey(key: $genericParamName) ?
                    $genericParamsCustomRegex->get(key: $genericParamName) :
                    $genericParamDefaultRegex;

                $genericParamsRegexAssociation[$matches[0][$key]] = "($regex)";
                $genericParamsOrderedNames[] = $genericParamName;
            }
        }

        // replace all generic params with their associated regex
        $resultArray = [];
        $finalPattern = str_replace(search: "/",replace: "\/",subject: $pattern);
        $finalPattern = @str_replace(
            search: array_keys($genericParamsRegexAssociation),
            replace: array_values(array: $genericParamsRegexAssociation),
            subject: $finalPattern
        );

        # check the match between the pattern and the link
        if(!@preg_match(pattern: "#^$finalPattern$#",subject: $this->baseUri,matches: $matches))
            return null;

        # associate generic params with their matched elements
        if(!empty($matches[1]))
            array_shift(array: $matches);

        foreach($genericParamsOrderedNames as $key => $genericParamName)
            $resultArray[$genericParamName] = !empty($matches[$key]) ? $matches[$key] : null;

        return $resultArray;
    }

    /**
     * @brief format the pattern by adding the management of the start and end ending /
     * @param string $pattern pattern
     * @return string the formated pattern
     */
    protected function formatPattern(string $pattern):string{
        if(!str_starts_with(haystack: $pattern,needle: "/"))
            $pattern = "/$pattern";

        if(!str_ends_with(haystack: $pattern,needle: "/?"))
            $pattern = "$pattern/?";

        return $pattern;
    }
}