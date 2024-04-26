<?php

namespace SaboCore\Database\Default\System;

use ReflectionClass;
use ReflectionException;

/**
 * @brief Fournisseur de création de table sql à partir d'un model
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class MysqlTableCreator{
    /**
     * @brief Fourni le sql de création de table à partir d'un model
     * @param MysqlModel $model Le model
     * @return string la chaine sql de création
     * @throws MysqlException en cas d'erreur
     * @throws ReflectionException en cas d'erreur de réflexion
     */
    public static function getTableCreationFrom(MysqlModel $model):string{
        $creationScript = "{$model->getTableNameManager()->getCreationSql()}(\n";

        $primaryKeys = [];
        $foreignKeys = [];

        foreach($model->getColumnsConfig() as $_ => $column){
            $creationScript .= "\t{$column->getCreationSql()},\n";

            if($column->isPrimaryKey())
                $primaryKeys[] = $column->getColumnName();

            if($column->isForeignKey()){
                // création du modèle référencé pour récupérer le nom de la table ainsi que la colonne référencée
                $reflection = new ReflectionClass(objectOrClass: $column->getReferencedModel());

                $referencedModel = $reflection->newInstance();
                $referencedModelColumnsConfig = $referencedModel->getDbColumnsConfig();

                $foreignKeys[] = [
                    "columnName" => $column->getColumnName(),
                    "referencedTable" => $referencedModel->getTableName()->getTableName(),
                    "referencedColumnName" => $referencedModelColumnsConfig[$column->getReferencedAttributeName()]->getColumnName()
                ];
            }
        }

        // ajout des clés primaires
        if(!empty($primaryKeys))
            $creationScript .= "\tPRIMARY KEY (". implode(separator: ",",array: $primaryKeys)."),\n";

        // ajout des clés étrangères
        foreach($foreignKeys as $foreignKey){
            $creationScript .= "\tFOREIGN KEY({$foreignKey["columnName"]}) REFERENCES {$foreignKey["referencedTable"]}({$foreignKey["referencedColumnName"]}),\n";
        }

        if(str_ends_with(haystack: $creationScript,needle: ",\n") )
            $creationScript = substr(string: $creationScript,offset: 0,length: -2) . "\n";

        return $creationScript . ");";
    }
}