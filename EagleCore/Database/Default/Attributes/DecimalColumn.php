<?php

namespace Yahvya\EagleFramework\Database\Default\Attributes;

use Attribute;
use Override;
use PDO;
use Yahvya\EagleFramework\Database\Default\Conditions\Cond;
use Yahvya\EagleFramework\Database\Default\Formater\Formater;

/**
 * @brief Decimal column type
 */
#[Attribute]
class DecimalColumn extends TableColumn
{
    /**
     * @var int Number of digits in the decimal part
     */
    protected int $decimalCount;

    /**
     * @var int Number of digits after the decimal part
     */
    protected int $afterDecimalCount;

    /**
     * @param string $columnName Column name in the database
     * @param bool $isNullable If the field is nullable (set to false by default if it is a primary key)
     * @param bool $isPrimaryKey If the field is a primary key
     * @param bool $isUnique If the field is unique
     * @param string $defaultValue Default value of the attribute (in SQL format)
     * @param bool $isForeignKey If the field is a foreign key
     * @param string|null $referencedModel Class of the model referenced by the key
     * @param string|null $referencedAttributeName Name of the referenced attribute
     * @param Cond[] $setConditions Conditions to check on the original data before accepting it in the attribute
     * @param Formater[] $dataFormatters Data formatter to transform the original data
     * @param Formater[] $datasReformers Data formatter to reformat the data
     * @param int $decimalCount Number of digits in the decimal part
     * @param int $afterDecimalCount Number of digits after the decimal part
     * @attention Conditions are applied before formatting on the original data
     * @attention Each formatter will receive the result of the previous one
     * @attention The default attribute must contain the exact string to be used in the SQL creation, e.g.: "'default'" "10" ...
     */
    public function __construct(string $columnName, bool $isNullable = false, bool $isPrimaryKey = false, bool $isUnique = false, string $defaultValue = self::NO_DEFAULT_VALUE, bool $isForeignKey = false, ?string $referencedModel = null, ?string $referencedAttributeName = null, array $setConditions = [], array $dataFormatters = [], array $datasReformers = [], int $decimalCount = 15, int $afterDecimalCount = 2)
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

        $this->decimalCount = $decimalCount;
        $this->afterDecimalCount = $afterDecimalCount;
    }

    #[Override]
    public function getCreationSql(): string
    {
        return
            "$this->columnName DECIMAL($this->decimalCount,$this->afterDecimalCount)"
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
