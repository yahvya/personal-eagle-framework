<?php

namespace SaboCore\Database\Default\QueryBuilder;

use Exception;

/**
 * @brief Ajout des clauses where
 * @author yahaya bathily https://github.com/yahvya
 */
trait Where{
    /**
     * @brief Ajoute la clause where
     * @return QueryBuilder this
     */
    public function where():QueryBuilder{
        $this->sqlString .= "WHERE ";
        
        return $this;
    }

    /**
     * @brief Ajoute une condition where
     * @param string $attributeName nom de l'attribut
     * @param mixed $value valeur à comparer
     * @param SqlComparator $comparator l'opérateur de comparaison à utiliser par défaut =
     * @param SqlSeparator|null $nextSeparator le séparateur pour ajouter une condition par la suite ou null si rien
     * @return QueryBuilder this
     * @throws Exception en cas d'erreur
     */
    public function whereCond(string $attributeName,mixed $value,SqlComparator $comparator = SqlComparator::EQUAL,?SqlSeparator $nextSeparator = null):QueryBuilder{
        $this->sqlString .= $this->manageCond($attributeName,$value,$comparator,$nextSeparator);

        return $this;
    }

    /**
     * @brief Alias à whereGroup rajoute un séparateur après le groupe
     * @param SqlSeparator $separator le séparateur
     * @param array ...$conditionsToGroup paramètres multiples, tableaux représentant les arguments de whereCond
     * @return QueryBuilder this
     */
    public function whereGroupSep(SqlSeparator $separator,array ...$conditionsToGroup):QueryBuilder{
        $this->whereGroup(...$conditionsToGroup);

        $this->sqlString .= " $separator->value ";

        return $this;
    }

    /**
     * @brief Crée une condition groupée
     * @param array ...$conditionsToGroup paramètres multiples, tableaux représentant les arguments de whereCond
     * @return QueryBuilder this
     */
    public function whereGroup(array ...$conditionsToGroup):QueryBuilder{
        $sqlString = "(";

        foreach($conditionsToGroup as $condData) $sqlString .= call_user_func_array([$this,"manageCond"],$condData);

        $sqlString .= ") ";

        $this->sqlString .= $sqlString;

        return $this;
    }

    /**
     * @brief Ajoute un séparateur dans la requête
     * @param SqlSeparator $sep le séparateur
     * @return QueryBuilder this
     */
    public function addSep(SqlSeparator $sep):QueryBuilder{
        $this->sqlString .= " $sep->value ";
        
        return $this;
    }       

    /**
     * @brief Ajoute une condition where
     * @param string $attributeName nom de l'attribut
     * @param mixed $value valeur à comparer
     * @param SqlComparator $comparator l'opérateur de comparaison à utiliser par défaut =
     * @param SqlSeparator|null $nextSeparator le séparateur pour ajouter une condition par la suite ou null si rien
     * @return string la chaine sql ou une chaine vide
     * @throws Exception en cas d'erreur
     */
    private function manageCond(string $attributeName,mixed $value,SqlComparator $comparator = SqlComparator::EQUAL,?SqlSeparator $nextSeparator = null):string{
        $columnName = $this->getAttributeLinkedColName($attributeName);

        if($columnName == null) return "";

        $sqlString = "$this->as.$columnName $comparator->value ? ";

        if($nextSeparator != null) $sqlString .= " $nextSeparator->value ";

        $this->toBind[] = $value;

        return $sqlString;
    }
}