<?php

namespace SaboCore\Database\Default\QueryBuilder;

use Exception;

/**
 * @brief Crée le select d'une requête
 * @author yahaya bathily https://github.com/yahvya
 */
trait Select{
    /**
     * @brief Ajoute le sql select
     * @param string|array ...$toSelect Paramètres multiples, le nom des attributs du model liés aux colonnes à récupérer ou [SqlFunction, NomAttribut,@optionnal alias], si vide select * par défaut
     * @return QueryBuilder this
     * @throws Exception en cas d'erreur
     */
    public function select(string|array ...$toSelect):QueryBuilder{
        $this->sqlString = "SELECT";

        if(!empty($toSelect) ){
            $toGet = [];

            foreach($toSelect as $attributeData){
                if(gettype($attributeData) == "array"){
                    $columnName = $this->getAttributeLinkedColName($attributeData[1]);

                    if($columnName == null) continue;

                    $columnName = "{$attributeData[0]->value}($this->as.$columnName)";

                    if(!empty($attributeData[2]) ) $columnName .= " AS $attributeData[2]";
                }
                else{
                    $columnName = $this->getAttributeLinkedColName($attributeData);

                    if($columnName == null) continue;

                    $columnName = "$this->as.$columnName";
                }

                $toGet[] = $columnName;
            }

            $this->sqlString .= " " . implode(",",$toGet) . " ";
        }
        else $this->sqlString .= " * ";

        $this->sqlString .= "FROM {$this->linkedModel->getTableName()} AS $this->as ";

        return $this;
    }
}