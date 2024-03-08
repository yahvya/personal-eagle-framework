<?php

namespace SaboCore\Routing\Routes;

/**
 * @brief Résultat de match
 * @author yahaya bathily https://github.com/yahvya
 */
class MatchResult{
    /**
     * @var bool si le match est fait
     */
    protected bool $match;

    /**
     * @var array table de match
     */
    protected array $matchTable;

    /**
     * @param bool $haveMatch si le match est fait
     * @param array $matchTable table de match
     */
    public function __construct(bool $haveMatch,array $matchTable = []){
        $this->matchTable = $matchTable;
        $this->match = $haveMatch;
    }

    /**
     * @return bool si le match est fait
     */
    public function getHaveMatch():bool{
        return $this->match;
    }

    /**
     * @return array table de match
     */
    public function getMatchTable():array{
        return $this->matchTable;
    }
}