<?php

namespace SaboCore\Database\Default\System;

use Closure;
use SaboCore\Database\System\DatabaseComparator;

/**
 * @brief Comparateurs mysql
 * @author yahaya bathily https://github.com/yahvya
 */
class MysqlComparator extends DatabaseComparator{
    /**
     * @var Closure fonction de récupération des données de bind
     */
    protected Closure $toBindGetter;

    /**
     * @param string $comparator Comparateur
     * @param Closure $toBindGetter Fonction de récupération des données de bind du comparateur
     * @attention Fonction doit retourner MysqlBindDatas
     */
    public function __construct(string $comparator,Closure $toBindGetter){
        parent::__construct($comparator);

        $this->toBindGetter = $toBindGetter;
    }

    /**
     * @brief Fourni les données de bind PDO à partir des données fournies
     * @param mixed $value Données
     * @return MysqlBindDatas Données de bind au format
     */
    public function getBindDatas(mixed $value):MysqlBindDatas{
        return call_user_func_array($this->toBindGetter,[$value]);
    }

    /**
     * @return MysqlComparator Comparateur égal
     * @attention Donnée simple
     */
    public static function EQUAL():MysqlComparator{
        return new MysqlComparator(
            comparator: "= {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur in
     * @attention Tableau contenant les valeurs à vérifier
     */
    public static function IN():MysqlComparator{
        return new MysqlComparator(
            comparator: "IN({bindMarkers})",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: count($value),
                    toBindDatas: array_map(
                        callback: fn(mixed $value):array => [$value],
                        array: $value
                    )
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur >
     * @attention Donnée simple
     */
    public static function SUPERIOR():MysqlComparator{
        return new MysqlComparator(
            comparator: "> {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur <
     * @attention Donnée simple
     */
    public static function INFERIOR():MysqlComparator{
        return new MysqlComparator(
            comparator: "< {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur >=
     * @attention Donnée simple
     */
    public static function SUPERIOR_OR_EQUAL():MysqlComparator{
        return new MysqlComparator(
            comparator: ">= {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur <=
     * @attention Donnée simple
     */
    public static function INFERIOR_OR_EQUAL():MysqlComparator{
        return new MysqlComparator(
            comparator: "<= {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur !=
     * @attention Donnée simple
     */
    public static function NOT_EQUAL():MysqlComparator{
        return new MysqlComparator(
            comparator: "!= {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur BETWEEN
     * @attention Tableau contenant les deux valeurs min max
     */
    public static function BETWEEN():MysqlComparator{
        return new MysqlComparator(
            comparator: "BETWEEN {singleMarker} AND {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 2,
                    toBindDatas: [ [$value[0]],[$value[1]] ]
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur LIKE
     * @attention Donnée simple regex like mysql
     */
    public static function LIKE():MysqlComparator{
        return new MysqlComparator(
            comparator: "LIKE {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur LIKE
     * @attention Donnée simple regex
     */
    public static function REGEXP():MysqlComparator{
        return new MysqlComparator(
            comparator: "REGEXP {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur comparaison null sécurisé
     * @attention Donnée simple
     */
    public static function SECURE_COMPARE():MysqlComparator{
        return new MysqlComparator(
            comparator: "<=> {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator Comparateur par son
     * @attention Donnée simple
     */
    public static function SOUNDS_LIKE():MysqlComparator{
        return new MysqlComparator(
            comparator: "SOUNDS LIKE {singleMarker}",
            toBindGetter: function(mixed $value):MysqlBindDatas{
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }
}
