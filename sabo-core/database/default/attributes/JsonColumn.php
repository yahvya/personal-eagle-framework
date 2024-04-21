<?php

namespace SaboCore\Database\Default\Attributes;

use Attribute;
use Override;
use PDO;

/**
 * @brief Champs de type json
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class JsonColumn extends TableColumn{
    public function __construct(string $columnName,bool $isNullable = false, bool $isPrimaryKey = false, bool $isUnique = false, string $defaultValue = self::NO_DEFAULT_VALUE, bool $isForeignKey = false, ?string $referencedModel = null, ?string $referencedAttributeName = null, array $setConditions = [], array $dataFormatters = [], array $datasReformers = []){
        parent::__construct(
            columnName: $columnName,
            isNullable: $isNullable,
            isPrimaryKey: $isPrimaryKey,
            isUnique: $isUnique,
            defaultValue: $defaultValue,
            isForeignKey: $isForeignKey,
            referencedModel: $referencedModel,
            referencedAttributeName: $referencedAttributeName,
            setConditions: $setConditions,
            dataFormatters: $dataFormatters,
            datasReformers: $datasReformers
        );
    }

    #[Override]
    public function getCreationSql(): string{
        return
            "$this->columnName JSON"
            . ($this->isNullable ? "" : " NOT NULL")
            . ($this->isUnique() ? " UNIQUE": "")
            . ($this->haveDefaultValue() ? " DEFAULT {$this->getDefaultValueStr()}" : "");
    }

    #[Override]
    public function getColumnType():int{
        return PDO::PARAM_STR;
    }
}