<?php

namespace Yahvya\EagleFramework\Database\Default\Attributes;

use Yahvya\EagleFramework\Database\Default\Conditions\Cond;
use Yahvya\EagleFramework\Database\Default\Conditions\MysqlCondException;
use Yahvya\EagleFramework\Database\Default\Formater\Formater;
use Yahvya\EagleFramework\Database\Default\Formater\FormaterException;
use Yahvya\EagleFramework\Database\Default\System\MysqlModel;

/**
 * @brief Column abstract definition
 */
abstract class TableColumn extends SqlAttribute
{
    /**
     * @brief Represent that the attribute has no default value
     */
    protected const string NO_DEFAULT_VALUE = "ATTRIBUTE_NO_DEFAULT_VALUE";

    /**
     * @var bool Si le champ est nullable
     */
    protected bool $isNullable;

    /**
     * @var string|null Classe référencée par la clé étrangère
     */
    protected ?string $referencedModel;

    /**
     * @param string $columnName Column name inside the database
     * @param bool $isNullable If the field is nullable
     * @param bool $isPrimaryKey If the field is a part of the primary key
     * @param bool $isUnique If the field is unique
     * @param string $defaultValue SQL format default value for the attribute
     * @param bool $isForeignKey If the field is a foreign key
     * @param string|null $referencedModel Referenced model class (if the field is a foreign key)
     * @param string|null $referencedAttributeName Referenced model class attribute name (if the field is a foreign key)
     * @param Cond[] $setConditions Conditions to check when try to set a value in the associated attribute
     * @param Formater[] $dataFormatters Data formater
     * @param Formater[] $datasReformers Data reformers
     * @attention The conditions are called before the formatting on the provided data
     * @attention Every formater will receive the result of the previous one
     * @attention The default attribute must contain the exact past string. "default" "10" ...
     */
    public function __construct(
        protected(set) string $columnName,
        bool     $isNullable = false,
        protected(set) bool $isPrimaryKey = false,
        protected(set) bool $isUnique = false,
        protected(set) string $defaultValue = self::NO_DEFAULT_VALUE,
        protected(set) bool $isForeignKey = false,
        ?string  $referencedModel = null,
        protected(set) ?string $referencedAttributeName = null,
        protected(set) array $setConditions = [],
        protected(set) array $dataFormatters = [],
        protected(set) array $datasReformers = [])
    {
        $this->isNullable = $isPrimaryKey ? false : $isNullable;
        $this->referencedModel = $isForeignKey ? $referencedModel : null;
    }

    /**
     * @brief Check the data to set
     * @param MysqlModel $baseModel Model instance
     * @param string $attributeName Attribute name
     * @param mixed $data Data to verify
     * @return $this
     * @throws MysqlCondException On error
     */
    public function verifyData(MysqlModel $baseModel, string $attributeName, mixed $data): TableColumn
    {
        if ($this->isNullable && $data === null)
            return $this;

        foreach ($this->setConditions as $cond)
        {
            if (!$cond->verifyData(baseModel: $baseModel, attributeName: $attributeName, data: $data))
                throw new MysqlCondException(failedCond: $cond);
        }

        return $this;
    }

    /**
     * /**
     * @brief Formats the original data using the formatters
     * @param MysqlModel $baseModel Base model
     * @param mixed $originalData Original data
     * @return mixed The fully formatted data
     * @attention Conditions must be checked before formatting
     * @throws FormaterException In case of a formatting error
     * /
     */
    public function formatData(MysqlModel $baseModel, mixed $originalData): mixed
    {
        if ($originalData === null)
            return null;

        $formatedData = $originalData;

        foreach ($this->dataFormatters as $formatter)
            $formatedData = $formatter->format(baseModel: $baseModel, data: $formatedData);

        return $formatedData;
    }

    /**
     * @brief Reform the original data by passing the current values to the reformers
     * @param MysqlModel $baseModel Model instance
     * @param mixed $formatedData Formated data
     * @return mixed Reformed data
     * @throws FormaterException On formating error
     */
    public function reformData(MysqlModel $baseModel, mixed $formatedData): mixed
    {
        $reformedData = $formatedData;

        foreach ($this->datasReformers as $formatter)
            $reformedData = $formatter->format(baseModel: $baseModel, data: $formatedData);

        return $reformedData;
    }

    /**
     * @brief Method called when mounting the property bearing this attribute. Converts the data retrieved from the database into the final value
     * @param mixed $data The raw data
     * @return mixed The converted data
     */
    public function convertToValue(mixed $data): mixed
    {
        return $data;
    }

    /**
     * @brief Method called when inserting or updating the property bearing this attribute. Converts the attribute's value into a form that can be stored in the database
     * @param mixed $data The raw data
     * @return mixed The converted data
     */
    public function convertFromValue(mixed $data): mixed
    {
        return $data;
    }

    /**
     * @return bool Have a default value
     */
    public function haveDefaultValue(): bool
    {
        return $this->defaultValue !== self::NO_DEFAULT_VALUE;
    }

    /**
     * @return int Provide the PDO type
     */
    abstract public function getColumnType(): int;
}