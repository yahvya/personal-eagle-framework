<?php

namespace Yahvya\EagleFramework\Database\Default\System;

use Closure;
use Yahvya\EagleFramework\Database\Default\QueryBuilder\MysqlQueryBuilder;
use Yahvya\EagleFramework\Database\System\QueryComparator;

/**
 * @brief Comparateurs mysql
 * @attention To compare with a request's result, use the REQUEST_COMPARATOR
 * @notice Markers : singleMarker,bindMarkers
 */
class MysqlComparator extends QueryComparator
{

    /**
     * @param string $comparator Comparator
     * @param Closure $toBindGetter Closure to get the data to bind of the comparator
     * @attention The closure must return a MysqlBindDatas
     */
    public function __construct(string $comparator, protected(set) Closure $toBindGetter)
    {
        parent::__construct($comparator);
    }

    /**
     * @brief Provide pdo bind format data based on the provided values
     * @param mixed $value Value to associate
     * @return MysqlBindDatas Formated bind data
     */
    public function getBindDatas(mixed $value): MysqlBindDatas
    {
        return call_user_func_array($this->toBindGetter, [$value]);
    }

    /**
     * @brief Create a comparator on a resuest
     * @param string $comparator Comparison string containing a marker '{request}' to place the request in
     * @param MysqlQueryBuilder $queryBuilder Query Builder to treat
     * @attention The generated 'bindGetter' take as argument the query builder instance (on a "MysqlCondition" provide the queryBuilder)
     * @attention Once being called, the request content can't be modified
     * @return MysqlComparator Comparator
     */
    public static function REQUEST_COMPARATOR(string $comparator, MysqlQueryBuilder $queryBuilder): MysqlComparator
    {
        return new MysqlComparator(
            comparator: str_replace(search: "{request}", replace: $queryBuilder->getSql(), subject: $comparator),
            toBindGetter: function (MysqlQueryBuilder $queryBuilder): MysqlBindDatas {
                // merge of the values to bind of the builder inside one builder

                $bindValues = [];
                $countOfMarkers = 0;

                foreach ($queryBuilder->getBindValues() as $bindValue)
                {
                    $bindValues[] = $bindValue->getToBindDatas()->toArray();
                    $countOfMarkers += $bindValue->getCountOfMarkers();
                }

                return new MysqlBindDatas(
                    countOfMarkers: $countOfMarkers,
                    toBindDatas: $bindValues
                );
            }
        );
    }

    /**
     * @return MysqlComparator Equal comparator
     * @attention Simple data
     */
    public static function EQUAL(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "= {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator "in" comparator
     * @attention Array containing values to verify
     */
    public static function IN(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "IN({bindMarkers})",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: count($value),
                    toBindDatas: array_map(
                        callback: fn(mixed $value): array => [$value],
                        array: $value
                    )
                );
            }
        );
    }

    /**
     * @return MysqlComparator ">, superior" comparator
     * @attention Simple data
     */
    public static function SUPERIOR(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "> {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator "<, Inferior" comparator
     * @attention Simple data
     */
    public static function INFERIOR(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "< {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator ">=, superior or equal" comparator
     * @attention Simple data
     */
    public static function SUPERIOR_OR_EQUAL(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: ">= {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator "<=, inferior or equal" comparator
     * @attention Simple data
     */
    public static function INFERIOR_OR_EQUAL(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "<= {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator '!= , not equal' Comparator
     * @attention Simple data
     */
    public static function NOT_EQUAL(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "!= {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator 'BETWEEN' comparator
     * @attention Array containing mix and max value [min,max]
     */
    public static function BETWEEN(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "BETWEEN {singleMarker} AND {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 2,
                    toBindDatas: [[$value[0]], [$value[1]]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator LIKE
     * @attention Simple data (string)
     */
    public static function LIKE(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "LIKE {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator 'REGEXP' comparator
     * @attention Simple data (regex)
     */
    public static function REGEXP(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "REGEXP {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator NULL secure comparison comparator
     * @attention Data simple
     */
    public static function SECURE_COMPARE(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "<=> {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }

    /**
     * @return MysqlComparator 'Sounds like' comparator
     * @attention Simple data
     */
    public static function SOUNDS_LIKE(): MysqlComparator
    {
        return new MysqlComparator(
            comparator: "SOUNDS LIKE {singleMarker}",
            toBindGetter: function (mixed $value): MysqlBindDatas {
                return new MysqlBindDatas(
                    countOfMarkers: 1,
                    toBindDatas: [[$value]]
                );
            }
        );
    }
}
