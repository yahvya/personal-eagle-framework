<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * séparateurs sql pris en charge
 */
enum SqlSeparator:string{
    /**
     * comparaison and
     */
    case AND = "and";
    /**
     * comparaison or
     */
    case OR = "or";
    /**
     * order by desc
     */
    case DESC = "desc";
    /**
     * order by asc
     */
    case ASC = "asc";
}