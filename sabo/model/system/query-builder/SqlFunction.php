<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * liste des fonctions sql pris en charge
 */
enum SqlFunction:string{
    case COUNT = "count";
    case SUM = "sum";
    case AVG = "avg"; 
    case DISTINCT = "distinct";
}