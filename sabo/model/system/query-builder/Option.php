<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * options supplémentaires d'une requête
 */
trait Option{
    /**
     * ajoute la clause order by 
     * @param conds paramètres multiples, nom_attribut ou [nom_attribut,SqlSeparator(desc ou asc)] par défaut asc
     */
    public function orderBy(string|array... $conds):QueryBuilder{
        $toAdd = [];

        foreach($conds as $cond){
            if(gettype($cond) == "array"){
                $columnName = $this->getAttributeLinkedColName($cond[0]);
                
                array_push($toAdd,"{$this->as}.{$columnName} {$cond[1]->value}");
            }
            else array_push($toAdd,"{$this->as}.{$this->getAttributeLinkedColName($cond)} asc" );
        }

        $this->sqlString .= "order by " . implode(",",$toAdd) . " ";

        return $this;
    }
}