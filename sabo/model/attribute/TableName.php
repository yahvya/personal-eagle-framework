<?php

namespace Sabo\Model\Attribute;

use Attribute;

/**
 * attribut représentant le nom de la table
 */
#[Attribute]
class TableName{
    /**
     * nom de la table
     */
    private string $tableName;

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