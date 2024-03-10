<?php

namespace SaboCore\Database\Default\QueryBuilder;

/**
 * @brief Utilitaire de jointure
 * @author yahaya bathily https://github.com/yahvya
 */
trait Join{
    /**
     * @brief Fusionne les données du query builder passé avec ceux du QueryBuilder actuel
     * @param QueryBuilder $toJoin Le queryBuilder à joindre
     * @param string|null $prefixSqlLinker Sql à ajouter avant fusion
     * @param string|null $suffixSqlLinker Sql à ajouter après fusion
     * @return QueryBuilder this
     */
    public function joinQuery(QueryBuilder $toJoin,?string $prefixSqlLinker = null,?string $suffixSqlLinker = null):QueryBuilder{
        if($prefixSqlLinker != null) $this->sqlString .= "$prefixSqlLinker ";
        $this->sqlString .= "({$toJoin->getSqlString()}) ";
        if($suffixSqlLinker != null) $this->sqlString .= "$suffixSqlLinker ";

        $this->toBind = array_merge_recursive($this->toBind,$toJoin->getToBind() );
        
        return $this;
    }   

    /**
     * @brief Ajoute un sql écrit
     * @param string $sql la chaine sql à ajouter
     * @param array $toBind les valeurs à bind
     * @return QueryBuilder this
     */
    public function addSql(string $sql,array $toBind = []):QueryBuilder{
        $this->sqlString .= "$sql ";
        $this->toBind = array_merge_recursive($this->toBind,$toBind);

        return $this;
    }
}