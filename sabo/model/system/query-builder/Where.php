<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * ajout des clauses where
 */
trait Where{
    /**
     * ajoute la clause where
     * @return this
     */
    public function where():QueryBuilder{
        $this->sqlString .= "where ";
        
        return $this;
    }

    /**
     * ajoute une condition where
     * @param attributeName nom de l'attribut
     * @param value valeur à comparer
     * @param comparator l'opérateur de comparaison à utiliser par défaut =
     * @param nextSeparator le séparateur pour ajouter une condition par la suite ou null si rien
     * @return this
     */
    public function whereCond(string $attributeName,mixed $value,SqlComparator $comparator = SqlComparator::EQUAL,?SqlSeparator $nextSeparator = null):QueryBuilder{
        $this->sqlString .= $this->manageCond($attributeName,$value,$comparator,$nextSeparator);

        return $this;
    }

    /**
     * alias a whereGroup rajoute un séparateur après le groupe
     * @param separator le séparateur
     * @param param condsToGroup paramètres multiples, tableaux représentant les arguments de whereCond
     */
    public function whereGroupSep(SqlSeparator $separator,array... $condsToGroup):QueryBuilder{
        $this->whereGroup(...$condsToGroup);

        $this->sqlString .= " {$separator->value} ";

        return $this;
    }

    /**
     * crée une condition groupé
     * @param condsToGroup paramètres multiples, tableaux représentant les arguments de whereCond
     */
    public function whereGroup(array... $condsToGroup):QueryBuilder{
        $sqlString = "(";

        foreach($condsToGroup as $condData) $sqlString .= call_user_func_array([$this,"manageCond"],$condData);

        $sqlString .= ") ";

        $this->sqlString .= $sqlString;

        return $this;
    }

    /**
     * ajoute un séparateur dans la requête
     * @param sep le séparateur
     * @return this
     */
    public function addSep(SqlSeparator $sep):QueryBuilder{
        $this->sqlString .= " {$sep->value} ";
        
        return $this;
    }       

    /**
     * ajoute une condition where
     * @param attributeName nom de l'attribut
     * @param value valeur à comparer
     * @param comparator l'opérateur de comparaison à utiliser par défaut =
     * @param nextSeparator le séparateur pour ajouter une condition par la suite ou null si rien
     * @return string la chaine sql ou une chaine vide
     */
    private function manageCond(string $attributeName,mixed $value,SqlComparator $comparator = SqlComparator::EQUAL,?SqlSeparator $nextSeparator = null):string{
        $columnName = $this->getAttributeLinkedColName($attributeName);

        if($columnName == null) return "";

        $sqlString = "{$this->as}.{$columnName} {$comparator->value} ? ";

        if($nextSeparator != null) $sqlString .= " {$nextSeparator->value} ";

        array_push($this->toBind,$value);

        return $sqlString;
    }
}