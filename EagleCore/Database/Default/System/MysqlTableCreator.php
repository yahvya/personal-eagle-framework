<?php

namespace Yahvya\EagleFramework\Database\Default\System;

use ReflectionClass;
use ReflectionException;

/**
 * @brief Mysql table SQL generator
 */
abstract class MysqlTableCreator
{
    /**
     * @brief Provide the table creation SQL string based on a model
     * @param MysqlModel $model Model instance
     * @return string Creation SQL string of the associated table
     * @throws MysqlException On error
     * @throws ReflectionException On reflection error
     */
    public static function getTableCreationFrom(MysqlModel $model): string
    {
        $creationScript = "{$model->tableNameProvider->getCreationSql()}(\n";

        $primaryKeys = [];
        $foreignKeys = [];

        foreach ($model->dbColumnsConfig as $column)
        {
            $creationScript .= "\t{$column->getCreationSql()},\n";

            if ($column->isPrimaryKey())
                $primaryKeys[] = $column->getColumnName();

            if ($column->isForeignKey())
            {
                // Build an instance of the referenced model instance
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

        if (!empty($primaryKeys))
            $creationScript .= "\tPRIMARY KEY (" . implode(separator: ",", array: $primaryKeys) . "),\n";

        foreach ($foreignKeys as $foreignKey)
        {
            $creationScript .= "\tFOREIGN KEY({$foreignKey["columnName"]}) REFERENCES {$foreignKey["referencedTable"]}({$foreignKey["referencedColumnName"]}),\n";
        }

        if (str_ends_with(haystack: $creationScript, needle: ",\n"))
            $creationScript = substr(string: $creationScript, offset: 0, length: -2) . "\n";

        return $creationScript . ");";
    }
}