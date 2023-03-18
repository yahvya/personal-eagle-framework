<?php

namespace Sabo\Model\System\QueryBuilder;

use Exception;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;

/**
 * représente une requête update
 */
trait Update{
    /**
     * ajoute la requête sql de mise à nour
     * @param toUpddate tableau représentant les valeurs à mettre à jour [nom_attribut => nouvelleValeur]
     * @return this
     * @throws Exception (en mode debug) si aucune valeur à mettre à jour
     */
    public function update(array $toUpdate):QueryBuilder{
        $this->sqlString = "update {$this->linkedModel->getTableName()} as {$this->as} set ";

        $toJoin = [];

        // ajout des valeurs set
        foreach($toUpdate as $attributeName => $value){
            $columnName = $this->getAttributeLinkedColName($attributeName);

            array_push($toJoin,"{$this->as}.{$columnName} = ?");
            array_push($this->toBind,$value);
        }

        if(count($toJoin) == 0){
            if(SaboConfig::getBoolConfig(SaboConfigAttributes::DEBUG_MODE) )
                throw new Exception("Aucune valeur à mettre à jour");
            else    
                return $this;
        }

        $this->sqlString .= implode(",",$toJoin) . " ";
        
        return $this;
    }
}