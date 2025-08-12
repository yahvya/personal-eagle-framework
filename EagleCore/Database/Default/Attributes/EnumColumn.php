<?php

namespace Yahvya\EagleFramework\Database\Default\Attributes;

use Attribute;
use Override;
use PDO;
use Yahvya\EagleFramework\Utils\List\EagleList;

/**
 * @brief Enumeration column type
 */
#[Attribute]
class EnumColumn extends TableColumn
{
    /**
     * @var EagleList<string> Enum possible values
     */
    protected(set) EagleList $possibleValues;

    public function __construct(string $columnName, array $possibleValues, bool $isNullable = false, bool $isPrimaryKey = false, bool $isUnique = false, string $defaultValue = self::NO_DEFAULT_VALUE, bool $isForeignKey = false, ?string $referencedModel = null, ?string $referencedAttributeName = null, array $setConditions = [], array $dataFormatters = [], array $datasReformers = [])
    {
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

        $this->possibleValues = new EagleList(datas: $possibleValues);
    }

    #[Override]
    public function getCreationSql(): string
    {
        return
            "$this->columnName ENUM(" .
            implode(
                separator: ",",
                array: array_map(
                    callback: fn(string $value): string => "'$value'",
                    array: $this->possibleValues->toArray()
                )
            )
            . ")"
            . ($this->isNullable ? "" : " NOT NULL")
            . ($this->isUnique() ? " UNIQUE" : "")
            . ($this->haveDefaultValue() ? " DEFAULT {$this->getDefaultValueStr()}" : "");
    }

    #[Override]
    public function getColumnType(): int
    {
        return PDO::PARAM_STR;
    }
}