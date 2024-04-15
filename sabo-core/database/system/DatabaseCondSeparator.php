<?php

namespace SaboCore\Database\System;

/**
 * @brief Représentation de la séparation entre deux conditions
 * @author yahaya bathily https://github.com/yahvya
 */
class DatabaseCondSeparator{
    /**
     * @var string Séparateur
     */
    protected string $separator;

    /**
     * @param string $separator Séparateur
     */
    protected function __construct(string $separator){
        $this->separator = $separator;
    }

    /**
     * @return string Séparateur
     */
    public function getSeparator(): string{
        return $this->separator;
    }
}
