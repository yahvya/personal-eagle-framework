<?php

namespace SaboCore\Database\Default\Attribute;

use Attribute;

/**
 * @brief Attribut représentant le nom de la table
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class TableName{
    /**
     * @brief Nom de la table
     */
    private string $tableName;

    /**
     * @param string $tableName le nom de la table en base de données
     */
    public function __construct(string $tableName){
        $this->tableName = $tableName;
    }

    /**
     * @return string le nom de la table
     */
    public function getTableName():string{
        return $this->tableName;
    }
}