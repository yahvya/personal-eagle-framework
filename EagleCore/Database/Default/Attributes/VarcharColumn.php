<?php

namespace Yahvya\EagleFramework\Database\Default\Attributes;

use Attribute;
use Override;
use PDO;
use Yahvya\EagleFramework\Database\Default\Conditions\Cond;
use Yahvya\EagleFramework\Database\Default\Formater\Formater;

/**
 * @brief Column of type varchar
 */
#[Attribute]
class VarcharColumn extends TableColumn
{
    /**
     * @var int Maximum length of the field
     */
    protected int $maxLen;

    /**
     * @param string $columnName Name of the database column
     * @param int $maxLen Maximum length of the field
     * @param bool $isNullable Whether the field is nullable (default false if primary key)
     * @param bool $isPrimaryKey Whether the field is a primary key
     * @param bool $isUnique Whether the field is unique
     * @param string $defaultValue Default value of the attribute (in SQL format)
     * @param bool $isForeignKey Whether the field is a foreign key
     * @param string|null $referencedModel Class of the model referenced by the key
     * @param string|null $referencedAttributeName Name of the referenced attribute
     * @param Cond[] $setConditions Conditions to check on the original data before accepting it into the attribute
     * @param Formater[] $dataFormatters Data formatters to transform the original data
     * @param Formater[] $datasReformers Data formatters to reformat the data
     * @attention Conditions are checked before formatting the original data
     * @attention Each formatter receives the result of the previous one
     * @attention The default attribute must contain the exact string that will be used in the SQL creation, e.g., "'default'", "10", etc.
     */
    public function __construct(string $columnName, int $maxLen, bool $isNullable = false, bool $isPrimaryKey = false, bool $isUnique = false, string $defaultValue = self::NO_DEFAULT_VALUE, bool $isForeignKey = false, ?string $referencedModel = null, ?string $referencedAttributeName = null, array $setConditions = [], array $dataFormatters = [], array $datasReformers = [])
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

        $this->maxLen = $maxLen;
    }

    #[Override]
    public function getCreationSql(): string
    {
        return
            "$this->columnName VARCHAR($this->maxLen)"
            . ($this->isNullable ? "" : " NOT NULL")
            . ($this->isUnique ? " UNIQUE" : "")
            . ($this->haveDefaultValue() ? " DEFAULT $this->defaultValue" : "");
    }

    #[Override]
    public function getColumnType(): int
    {
        return PDO::PARAM_STR;
    }
}
