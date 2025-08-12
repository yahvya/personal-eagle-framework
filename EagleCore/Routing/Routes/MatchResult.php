<?php

namespace Yahvya\EagleFramework\Routing\Routes;

/**
 * @brief Match result
 */
class MatchResult
{
    /**
     * @param bool $haveMatch Whether the match is successful
     * @param array $matchTable Match table
     */
    public function __construct(
        protected(set) bool $haveMatch,
        protected(set) array $matchTable = []
    )
    {
    }
}
