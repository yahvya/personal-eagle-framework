<?php

namespace SaboCore\Database\System;

/**
 * @brief Condition de récupération
 * @author yahaya bathily https://github.com/yahvya
 */
class DatabaseCondition{
    /**
     * @var string Nom de l'attribut du model
     */
    protected string $attributeName;

    /**
     * @var mixed Valeur à vérifier
     */
    protected mixed $conditionValue;

    /**
     * @var DatabaseComparator Comparateur
     */
    protected DatabaseComparator $comparator;

    /**
     * @param string $attributeName Nom de l'attribut du model
     * @param DatabaseComparator $comparator Comparateur
     * @param mixed $conditionValue Valeur à vérifier
     */
    public function __construct(string $attributeName, DatabaseComparator $comparator,mixed $conditionValue){
        $this->attributeName = $attributeName;
        $this->conditionValue = $conditionValue;
        $this->comparator = $comparator;
    }

    /**
     * @return string Nom de l'attribut du model
     */
    public function getAttributeName(): string{
        return $this->attributeName;
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