<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * ajout des clauses where
 */
trait Where{
    /**
     * ajoute la clause where
     * @return QueryBuilder this
     */
    public function where():QueryBuilder{
        $this->sqlString .= "where ";
        
        return $this;
    }

    /**
     * ajoute une condition where
     * @param string $attributeName nom de l'attribut
     * @param mixed $value valeur à comparer
     * @param SqlComparator $comparator l'opérateur de comparaison à utiliser par défaut =
     * @param SqlComparator|null $nextSeparator le séparateur pour ajouter une condition par la suite ou null si rien
     * @return QueryBuilder this
     */
    public function whereCond(string $attributeName,mixed $value,SqlComparator $comparator = SqlComparator::EQUAL,?SqlSeparator $nextSeparator = null):QueryBuilder{
        $this->sqlString .= $this->manageCond($attributeName,$value,$comparator,$nextSeparator);

        return $this;
    }

    /**
     * alias a whereGroup rajoute un séparateur après le groupe
     * @param SqlSeparator $separator le séparateur
     * @param array... $param condsToGroup paramètres multiples, tableaux représentant les arguments de whereCond
     * @return QueryBuilder this
     */
    public function whereGroupSep(SqlSeparator $separator,array... $condsToGroup):QueryBuilder{
        $this->whereGroup(...$condsToGroup);

        $this->sqlString .= " {$separator->value} ";

        return $this;
    }

    /**
     * crée une condition groupé
     * @param array ...condsToGroup paramètres multiples, tableaux représentant les arguments de whereCond
     * @return QueryBuilder this
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
     * @param SqlSeparator $sep le séparateur
     * @return QueryBuilder this
     */
    public function addSep(SqlSeparator $sep):QueryBuilder{
        $this->sqlString .= " {$sep->value} ";
        
        return $this;
    }       

    /**
     * ajoute une condition where
     * @param string $attributeName nom de l'attribut
     * @param mixed $value valeur à comparer
     * @param SqlComparator $comparator l'opérateur de comparaison à utiliser par défaut =
     * @param SqlSeparator|null $nextSeparator le séparateur pour ajouter une condition par la suite ou null si rien
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