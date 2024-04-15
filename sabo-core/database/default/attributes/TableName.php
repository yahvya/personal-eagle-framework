<?php

namespace SaboCore\Database\Default\Attributes;

use Attribute;

#[Attribute]
class TableName extends SqlAttribute {
    /**
     * @var string Nom de la table
     */
    protected string $tableName;

    /**
     * @param string $tableName Nom de la table
     */
    public function __construct(string $tableName){
        $this->tableName = $tableName;
    }

    /**
     * @return string Le nom de la table
     */
    public function getTableName() : string{
        return $this->tableName;
    }

    public function getCreationSql():string{
        return "CREATE TABLE $this->tableName";
    }
}