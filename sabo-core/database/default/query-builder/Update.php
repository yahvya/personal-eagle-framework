<?php

namespace SaboCore\Database\Default\QueryBuilder;

use Exception;
use SaboCore\Config\EnvConfig;
use SaboCore\Routing\Application\Application;

/**
 * @brief Représente une requête update
 * @author yahaya bathily https://github.com/yahvya
 */
trait Update{
    /**
     * @brief Ajoute la requête sql de mise à nour
     * @param array $toUpdate tableau représentant les valeurs à mettre à jour [nom_attribut → nouvelleValeur]
     * @return QueryBuilder this
     * @throws Exception (en mode debug) si aucune valeur à mettre à jour
     */
    public function update(array $toUpdate):QueryBuilder{
        $this->sqlString = "UPDATE {$this->linkedModel->getTableName()} AS $this->as SET ";

        $toJoin = [];

        // ajout des valeurs set
        foreach($toUpdate as $attributeName => $value){
            $columnName = $this->getAttributeLinkedColName($attributeName);

            $toJoin[] = "$this->as.$columnName = ?";
            $this->toBind[] = $value;
        }

        if(count($toJoin) == 0){
            if(Application::getEnvConfig()->getConfig(EnvConfig::DEV_MODE_CONFIG->value) )
                throw new Exception("Aucune valeur à mettre à jour");
            else    
                return $this;
        }

        $this->sqlString .= implode(",",$toJoin) . " ";
        
        return $this;
    }
}