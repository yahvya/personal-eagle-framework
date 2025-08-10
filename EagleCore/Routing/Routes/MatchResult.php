<?php

namespace Yahvya\EagleFramework\Routing\Routes;

/**
 * @brief Match result
 */
class MatchResult
{
    /**
     * @var bool Whether the match is successful
     */
    protected bool $match;

    /**
     * @var array Match table
     */
    protected(set) array $matchTable;

    /**
     * @param bool $haveMatch Whether the match is successful
     * @param array $matchTable Match table
     */
    public function __construct(bool $haveMatch, array $matchTable = [])
    {
        $this->matchTable = $matchTable;
        $this->match = $haveMatch;
    }

    /**
     * @return bool Whether the match is successful
     */
    public function getHaveMatch(): bool
    {
        return $this->match;
    }
}
