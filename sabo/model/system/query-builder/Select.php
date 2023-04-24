<?php

namespace Sabo\Model\System\QueryBuilder;

use Exception;
use Sabo\Config\SaboConfig;
use Sabo\Config\SaboConfigAttributes;

/**
 * crée le select d'une requête
 * @conteneur doit contenir les variables
 */
trait Select{
    /**
     * ajoute le sql select
     * @param toSelect paramètres multiple, le nom des attributs du model liés aux colonnes à récupérer ou [SqlFunction,nom_attribut,@optionnal alias], si vide select * par défaut
     * @return this
     */
    public function select(string|array... $toSelect):QueryBuilder{
        $this->sqlString = "select";

        if(!empty($toSelect) ){
            $toGet = [];

            foreach($toSelect as $attributeData){
                if(gettype($attributeData) == "array"){
                    $columnName = $this->getAttributeLinkedColName($attributeData[1]);

                    if($columnName == null) continue;

                    $columnName = "{$attributeData[0]->value}({$this->as}.{$columnName})";

                    if(!empty($attributeData[2]) ) $columnName .= " as {$attributeData[2]}";
                }
                else{
                    $columnName = $this->getAttributeLinkedColName($attributeData);

                    if($columnName == null) continue;

                    $columnName = "{$this->as}.{$columnName}";
                }

                array_push($toGet,$columnName);
            }

            $this->sqlString .= " " . implode(",",$toGet) . " ";
        }
        else $this->sqlString .= " * ";

        $this->sqlString .= "from {$this->linkedModel->getTableName()} as {$this->as} ";

        return $this;
    }
}