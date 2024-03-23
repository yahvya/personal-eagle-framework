<?php

namespace SaboCore\Database\Default\QueryBuilder;

use Exception;
use SaboCore\Config\EnvConfig;
use SaboCore\Routing\Application\Application;

/**
 * @brief Gestion du sql insert
 * @author yahaya bathily https://github.com/yahvya
 */
trait Insert{
    /**
     * @brief Ajoute le sql insert
     * @param array $values tableau représentant les valeurs à insérer [nom_attribut → valeur]
     * @return QueryBuilder this
     * @throws Exception (en mode debug) si values est bide
     */
    public function insert(array $values):QueryBuilder{
        if(empty($values) ){
            if(Application::getEnvConfig()->getConfig(name: EnvConfig::DEV_MODE_CONFIG->value) )
                throw new Exception(message: "Values ne peut être vide dans une requête insert");
            else
                return $this;
        }

        $columnsToInsert = [];
        $marks = [];

        // récupération des valeurs à insérer
        foreach($values as $attributeName => $value){
            $columnsToInsert[] = $this->getAttributeLinkedColName($attributeName);
            $marks[] = "?";
            $this->toBind[] = $value;
        }

        $this->sqlString = "INSERT INTO {$this->linkedModel->getTableName()} (" . implode(separator: ",",array: $columnsToInsert) . ") VALUES(" . implode(separator: ",",array: $marks) . ") ";

        return $this;
    }
}