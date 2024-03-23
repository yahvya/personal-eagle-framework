<?php

namespace SaboCore\Database\Default\QueryBuilder;

use Exception;
use PDO;

/**
 * @brief Options supplémentaires d'une requête
 * @author yahaya bathily https://github.com/yahvya
 */
trait Option{
    /**
     * @brief Ajoute la clause order by
     * @param string|array ...$conditions paramètres multiples, nom_attribut ou [nom_attribut,SqlSeparator(desc ou asc)] par défaut asc
     * @return QueryBuilder this
     * @throws Exception en cas d'erreur
     */
    public function orderBy(string|array ...$conditions):QueryBuilder{
        $toAdd = [];

        foreach($conditions as $cond){
            if(gettype(value: $cond) == "array"){
                $columnName = $this->getAttributeLinkedColName(attributeName: $cond[0]);
                
                $toAdd[] = "$this->as.$columnName {$cond[1]->value}";
            }
            else $toAdd[] = "$this->as.{$this->getAttributeLinkedColName(attributeName: $cond)} ASC";
        }

        $this->sqlString .= "ORDER BY " . implode(separator: ",",array: $toAdd) . " ";

        return $this;
    }

    /**
     * @brief Ajoute la clause limit
     * @param int $count nombre de valeurs
     * @param int|null $offset offset
     * @return QueryBuilder this
     */
    public function limit(int $count,?int $offset = null):QueryBuilder{
        if($offset == null){
            $this->sqlString .= "LIMIT ? ";
        
            $this->toBind[] = [$count, PDO::PARAM_INT];
        }
        else{
            $this->sqlString .= "LIMIT ? OFFSET ? ";

            array_push($this->toBind,[$count,PDO::PARAM_INT],[$offset,PDO::PARAM_INT]);
        }

        return $this;
    }
}