<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * représente une requête delete
 */
trait Delete{

    /**
     * requête delete
     * @return this
     */
    public function delete():QueryBuilder{
        $this->sqlString = "delete from {$this->linkedModel->getTableName()} as {$this->as} ";

        return $this;
    }

    /**
     * requete delete construis avec une clause where sur les clés primaires
     * @return this
     * @throws Exception (en mode debug si aucune clé primaire trouvé)
     */
    public function deleteFromPrimaryKeys():QueryBuilder{
        $whereCondArray = [];

        // récupération des clé primaires
        foreach($this->linkedModel->getColumnsConfiguration() as $attributeName => $columnConfiguration){
            if(!empty($columnConfiguration["configClass"]) && $columnConfiguration["configClass"]->getIsPrimaryKey() ){
                array_push($whereCondArray,[$attributeName,$this->linkedModel->getAttribute($attributeName),SqlComparator::EQUAL,SqlSeparator::AND]);
            }
        }

        unset($whereCondArray[count($whereCondArray) - 1][3]);

        $this
            ->delete()
            ->where()
            ->whereGroup(...$whereCondArray);

        return $this;
    }
}