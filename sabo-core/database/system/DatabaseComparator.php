<?php

namespace SaboCore\Database\System;

/**
 * @brief Représentation de la fonction de comparaison
 * @author yahaya bathily https://github.com/yahvya
 */
class DatabaseComparator{
    /**
     * @var string Comparateur
     */
    protected string $comparator;

    /**
     * @param string $comparator Comparateur
     */
    protected function __construct(string $comparator){
        $this->comparator = $comparator;
    }

    /**
     * @return string Comparateur
     */
    public function getComparator(): string{
        return $this->comparator;
    }
}
