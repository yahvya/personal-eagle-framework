<?php

namespace SaboCore\Database\Default\System;

/**
 * @brief Fonctions mysql
 * @author yahaya bathily https://github.com/yahvya
 */
class MysqlFunction{
    /**
     * @var string Chaine complète de la fonction
     */
    protected string $function;

    /**
     * @var bool Si un nom d'attribut doit être remplacé dans la fonction
     */
    protected bool $replaceAttributesName;

    /**
     * @var string|null alias de retour
     */
    protected ?string $alias;

    /**
     * @param string $function Chaine complète de la fonction → "COUNT({username})" "NOW()"
     * @param bool $replaceAttributeName Si un nom d'attribut doit être remplacé dans la fonction
     */
    public function __construct(string $function,bool $replaceAttributesName = true){
        $this->function = $function;
        $this->replaceAttributesName = $replaceAttributesName;
        $this->alias = null;
    }

    /**
     * @défini un alias sur la fonction
     * @param string $alias alias
     * @return $this
     */
    public function as(string $alias):MysqlFunction{
        $this->alias = "'$alias'";

        return $this;
    }

    /**
     * @return string|null l'alias ou null si aucun
     */
    public function getAlias():string|null{
        return $this->alias;
    }

    /**
     * @return bool Si les noms des attributs doivent être remplacés
     */
    public function haveToReplaceAttributesName():bool{
        return $this->replaceAttributesName;
    }

    /**
     * @return string La fonction
     */
    public function getFunction():string{
        return $this->function;
    }

    /**
     * @brief Fonction CONCAT mysql
     * @param string ...$toConcat Valeur à concaténer. Si un nom d'attribut est fourni entouré avec {}. ex: CONCAT("val1","{attributeOne}","val2")
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function CONCAT(string ...$toConcat):MysqlFunction{
        return new MysqlFunction(function: "CONCAT(" . implode(separator: ",",array: $toConcat) . ")");
    }

    /**
     * @brief Fonction SUBSTRING mysql
     * @param string $stringGetter valeur ou nom de l'attribut entouré avec {}. ex: SUBSTRING("value1",1,3) SUBSTRING("{username}",1,4)
     * @param int $start index de départ
     * @param int $length taille
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function SUBSTRING(string $stringGetter,int $start,int $length):MysqlFunction{
        return new MysqlFunction(function: "SUBSTRING($stringGetter,$start,$length)");
    }

    /**
     * @brief Fonction UPPER mysql
     * @param string $stringGetter valeur ou nom de l'attribut entouré avec {}. ex: UPPER("value1") UPPER("{username}")
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function UPPER(string $stringGetter):MysqlFunction{
        return new MysqlFunction(function: "UPPER($stringGetter)");
    }

    /**
     * @brief Fonction LOWER mysql
     * @param string $stringGetter valeur ou nom de l'attribut entouré avec {}. ex: LOWER("value1") LOWER("{username}")
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function LOWER(string $stringGetter):MysqlFunction{
        return new MysqlFunction(function: "LOWER($stringGetter)");
    }

    /**
     * @brief Fonction DISTINCT mysql
     * @param string $toDistinct valeur ou nom de l'attribut entouré avec {} ex : DISTINCT("*") DISTINCT("{username}")
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function DISTINCT(string $toDistinct):MysqlFunction{
        return new MysqlFunction(function: "DISTINCT $toDistinct");
    }

    /**
     * @brief Fonction LENGTH mysql
     * @param string $stringGetter valeur ou nom de l'attribut entouré avec {}. ex: LENGTH("value1") LENGTH("{username}")
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function LENGTH(string $stringGetter):MysqlFunction{
        return new MysqlFunction(function: "LENGTH($stringGetter)");
    }

    /**
     * @brief Fonction RAND mysql
     * @return MysqlFunction la fonction
     */
    public static function RAND():MysqlFunction{
        return new MysqlFunction(function: "RAND()");
    }

    /**
     * @brief Fonction ABS mysql
     * @param string $numberGetter valeur ou nom de l'attribut entouré avec {}. ex: ABS({price}) ABS(10)
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function ABS(string $numberGetter):MysqlFunction{
        return new MysqlFunction(function: "ABS($numberGetter)");
    }

    /**
     * @brief Fonction SUM mysql
     * @param string $numberGetter nom de l'attribut entouré avec {}. ex: SUM({price})
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function SUM(string $numberGetter):MysqlFunction{
        return new MysqlFunction(function: "SUM($numberGetter)");
    }

    /**
     * @brief Fonction AVG mysql
     * @param string $numberGetter nom de l'attribut entouré avec {}. ex: AVG({price})
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function AVG(string $numberGetter):MysqlFunction{
        return new MysqlFunction(function: "AVG($numberGetter)");
    }

    /**
     * @brief Fonction COUNT mysql
     * @param string $numberGetter nom de l'attribut entouré avec {}. ex: COUNT({price})
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function COUNT(string $numberGetter):MysqlFunction{
        return new MysqlFunction(function: "COUNT($numberGetter)");
    }

    /**
     * @brief Fonction MIN mysql
     * @param string $numberGetter nom de l'attribut entouré avec {}. ex: MIN({price})
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function MIN(string $numberGetter):MysqlFunction{
        return new MysqlFunction(function: "MIN($numberGetter)");
    }

    /**
     * @brief Fonction MAX mysql
     * @param string $numberGetter nom de l'attribut entouré avec {}. ex: MAX({price})
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function MAX(string $numberGetter):MysqlFunction{
        return new MysqlFunction(function: "MAX($numberGetter)");
    }

    /**
     * @brief Fonction ROUND mysql
     * @param string $numberGetter valeur ou nom de l'attribut entouré avec {}. ex: ROUND({price}) ROUND(10)
     * @param int $decimal Précision
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function ROUND(string $numberGetter,int $decimal = 2):MysqlFunction{
        return new MysqlFunction(function: "ROUND($numberGetter,$decimal)");
    }

    /**
     * @brief Fonction NOW mysql
     * @return MysqlFunction la fonction
     */
    public static function NOW():MysqlFunction{
        return new MysqlFunction(function: "NOW()");
    }

    /**
     * @brief Fonction NOW mysql
     * @return MysqlFunction la fonction
     */
    public static function TIMESTAMP():MysqlFunction{
        return new MysqlFunction(function: "NOW()");
    }

    /**
     * @brief Fonction DATE_FORMAT mysql
     * @param string $dateGetter valeur ou nom de l'attribut entouré avec {}. ex: DATE_FORMAT("'2024-02-17 12:20:30'","%Y") DATE_FORMAT({orderDate},"%Y")
     * @return MysqlFunction la fonction
     * @attention Cette fonction remplace les noms d'attributs par défaut
     */
    public static function DATE_FORMAT(string $dateGetter,string $format):MysqlFunction{
        return new MysqlFunction(function: "DATE_FORMAT($dateGetter,'$format')");
    }

    /**
     * @brief Colonne classique avec alias
     * @param string $attributeName nom de l'attribut
     * @param string $alias alias
     * @return MysqlFunction la fonction
     */
    public static function COLUMN_ALIAS(string $attributeName,string $alias):MysqlFunction{
        return (new MysqlFunction(function: $attributeName))->as(alias: $alias);
    }
}