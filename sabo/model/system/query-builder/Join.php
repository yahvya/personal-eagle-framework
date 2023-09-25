<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * utilitaire de jointure
 */
trait Join{
    /**
     * fusionne les données du query builder passé avec ceux du querybuilder actuel
     * @param QueryBuilder $toJoin le queryBuilder à joindre
     * @param string[null $prefixSqlLinker sql à ajouter avant fusion
     * @param string|null $suffixSqlLinker sql à ajouter après fusion
     * @return QueryBuilder this
     */
    public function joinQuery(QueryBuilder $toJoin,?string $prefixSqlLinker = null,?string $suffixSqlLinker = null):QueryBuilder{
        if($prefixSqlLinker != null) $this->sqlString .= "{$prefixSqlLinker} ";
        $this->sqlString .= "({$toJoin->getSqlString()}) ";
        if($suffixSqlLinker != null) $this->sqlString .= "{$suffixSqlLinker} ";

        $this->toBind = array_merge_recursive($this->toBind,$toJoin->getToBind() );
        
        return $this;
    }   

    /**
     * ajoute un sql écrit
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