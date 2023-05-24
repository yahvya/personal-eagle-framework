<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * utilitaire de jointure
 */
trait Join{
    /**
     * fusionne les données du query builder passé avec ceux du querybuilder actuel
     * @param toJoin le queryBuilder à joindre
     * @param prefixSqlLinker sql à ajouter avant fusion
     * @param suffixSqlLinker sql à ajouter après fusion
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
     * @param sql la chaine sql à ajouter
     * @param toBind les valeurs à bind
     * @return QueryBuilder this
     */
    public function addSql(string $sql,array $toBind = []):QueryBuilder{
        $this->sqlString .= "$sql ";
        $this->toBind = array_merge_recursive($this->toBind,$toBind);

        return $this;
    }
}