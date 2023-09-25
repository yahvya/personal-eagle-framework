<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * liste des fonctions sql pris en charge
 */
enum SqlFunction:string{
    /**
     * mysql count()
     */
    case COUNT = "count";
    /**
     * mysql sum()
     */
    case SUM = "sum";
    /**
     * mysql avg()
     */
    case AVG = "avg"; 
    /**
     * mysql distinct() - distinct * à mettre manuellement
     */
    case DISTINCT = "distinct";
}