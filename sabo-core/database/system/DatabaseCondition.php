<?php

namespace SaboCore\Database\System;

/**
 * @brief Condition de récupération
 * @author yahaya bathily https://github.com/yahvya
 */
class DatabaseCondition{
    /**
     * @var mixed Elément de récupération de la condition
     */
    protected mixed $condGetter;

    /**
     * @var mixed Valeur à vérifier
     */
    protected mixed $conditionValue;

    /**
     * @var DatabaseComparator Comparateur
     */
    protected DatabaseComparator $comparator;

    /**
     * @param mixed $condGetter Elément de récupération de la condition
     * @param DatabaseComparator $comparator Comparateur
     * @param mixed $conditionValue Valeur à vérifier
     */
    public function __construct(mixed $condGetter, DatabaseComparator $comparator,mixed $conditionValue){
        $this->condGetter = $condGetter;
        $this->conditionValue = $conditionValue;
        $this->comparator = $comparator;
    }

    /**
     * @return mixed Elément de récupération de la condition
     */
    public function getCondGetter(): mixed{
        return $this->condGetter;
    }

    /**
     * @return mixed Valeur à vérifier
     */
    public function getConditionValue(): mixed{
        return $this->conditionValue;
    }

    /**
     * @return DatabaseComparator Comparateur
     */
    public function getComparator(): DatabaseComparator{
        return $this->comparator;
    }
}