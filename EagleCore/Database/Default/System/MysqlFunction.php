<?php

namespace Yahvya\EagleFramework\Database\Default\System;

/**
 * @brief Mysql function
 */
class MysqlFunction
{
    /**
     * @var string|null alias de retour
     */
    protected(set) ?string $alias;

    /**
     * @param string $function Function complete string using markers → "COUNT({username})" "NOW()"
     * @param bool $replaceAttributesName If an attribute should be replaced in the function
     */
    public function __construct(
        protected(set) string $function,
        protected(set) bool $replaceAttributesName = true
    )
    {
        $this->alias = null;
    }

    /**
     * @défini An alias for the function result
     * @param string $alias The alias
     * @return $this
     */
    public function as(string $alias): MysqlFunction
    {
        $this->alias = "'$alias'";

        return $this;
    }

    /**
     * @brief Mysql CONCAT function
     * @param string ...$toConcat Values to concat or attribute name which can be encapsulated with {} ex: CONCAT("val1","{attributeOne}","val2")
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function CONCAT(string ...$toConcat): MysqlFunction
    {
        return new MysqlFunction(function: "CONCAT(" . implode(separator: ",", array: $toConcat) . ")");
    }

    /**
     * @brief Mysql SUBSTRING function
     * @param string $stringGetter Values or attribute name which can be encapsulated with {}. Ex: SUBSTRING("value1",1,3) SUBSTRING("{username}",1,4)
     * @param int $start Start index
     * @param int $length Length
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function SUBSTRING(string $stringGetter, int $start, int $length): MysqlFunction
    {
        return new MysqlFunction(function: "SUBSTRING($stringGetter,$start,$length)");
    }

    /**
     * @brief Mysql UPPER function
     * @param string $stringGetter Value or attribute name which can be encapsulated with {}. Ex: UPPER("value1") UPPER("{username}")
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function UPPER(string $stringGetter): MysqlFunction
    {
        return new MysqlFunction(function: "UPPER($stringGetter)");
    }

    /**
     * @brief Mysql LOWER function
     * @param string $stringGetter Value or attribute name which can be encapsulated with {}. Ex: LOWER("value1") LOWER("{username}")
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function LOWER(string $stringGetter): MysqlFunction
    {
        return new MysqlFunction(function: "LOWER($stringGetter)");
    }

    /**
     * @brief Mysql DISTINCT function
     * @param string $toDistinct Value or attribute name which can be encapsulated with {}. Ex: DISTINCT("*") DISTINCT("{username}")
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function DISTINCT(string $toDistinct): MysqlFunction
    {
        return new MysqlFunction(function: "DISTINCT $toDistinct");
    }

    /**
     * @brief Mysql LENGTH function
     * @param string $stringGetter Value or attribute name which can be encapsulated with {}. Ex: LENGTH("value1") LENGTH("{username}")
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function LENGTH(string $stringGetter): MysqlFunction
    {
        return new MysqlFunction(function: "LENGTH($stringGetter)");
    }

    /**
     * @brief Mysql RAND function
     * @return MysqlFunction The build function
     */
    public static function RAND(): MysqlFunction
    {
        return new MysqlFunction(function: "RAND()");
    }

    /**
     * @brief Mysql ABS function
     * @param string $numberGetter Attribute name encapsulated with {}. Ex: ABS({price}) ABS(10)
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function ABS(string $numberGetter): MysqlFunction
    {
        return new MysqlFunction(function: "ABS($numberGetter)");
    }

    /**
     * @brief Mysql SUM function
     * @param string $numberGetter Attribute name encapsulated with {}. Ex: SUM({price})
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function SUM(string $numberGetter): MysqlFunction
    {
        return new MysqlFunction(function: "SUM($numberGetter)");
    }

    /**
     * @brief Mysql AVG function
     * @param string $numberGetter Attribute name encapsulated with {}. Ex: AVG({price})
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function AVG(string $numberGetter): MysqlFunction
    {
        return new MysqlFunction(function: "AVG($numberGetter)");
    }

    /**
     * @brief Mysql COUNT function
     * @param string $numberGetter Attribute name encapsulated with {}. Ex: COUNT({price})
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function COUNT(string $numberGetter): MysqlFunction
    {
        return new MysqlFunction(function: "COUNT($numberGetter)");
    }

    /**
     * @brief Mysql MIN function
     * @param string $numberGetter Attribute name encapsulated with {}. Ex: MIN({price})
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function MIN(string $numberGetter): MysqlFunction
    {
        return new MysqlFunction(function: "MIN($numberGetter)");
    }

    /**
     * @brief Mysql MAX function
     * @param string $numberGetter Attribute name encapsulated with {}. Ex: MAX({price})
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function MAX(string $numberGetter): MysqlFunction
    {
        return new MysqlFunction(function: "MAX($numberGetter)");
    }

    /**
     * @brief Mysql ROUND function
     * @param string $numberGetter value or attribute name which can be encapsulated with {}. Ex: ROUND({price}) ROUND(10)
     * @param int $decimal Precision
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function ROUND(string $numberGetter, int $decimal = 2): MysqlFunction
    {
        return new MysqlFunction(function: "ROUND($numberGetter,$decimal)");
    }

    /**
     * @brief Mysql NOW function
     * @return MysqlFunction The built function
     */
    public static function NOW(): MysqlFunction
    {
        return new MysqlFunction(function: "NOW()");
    }

    /**
     * @brief Mysql TIMESTAMP function
     * @return MysqlFunction The built function
     */
    public static function TIMESTAMP(): MysqlFunction
    {
        return new MysqlFunction(function: "TIMESTAMP()");
    }

    /**
     * @brief Mysql DATE_FORMAT function
     * @param string $dateGetter value or attribute name which can be encapsulated with {}. Ex: DATE_FORMAT("'2024-02-17 12:20:30'","%Y") DATE_FORMAT({orderDate},"%Y")
     * @return MysqlFunction The built function
     * @attention The method replaces the default name of the attributes
     */
    public static function DATE_FORMAT(string $dateGetter, string $format): MysqlFunction
    {
        return new MysqlFunction(function: "DATE_FORMAT($dateGetter,'$format')");
    }

    /**
     * @brief Put an alias on a column
     * @param string $attributeName Attribute name
     * @param string $alias Alias
     * @return MysqlFunction The built function
     */
    public static function COLUMN_ALIAS(string $attributeName, string $alias): MysqlFunction
    {
        return new MysqlFunction(function: $attributeName)->as(alias: $alias);
    }
}