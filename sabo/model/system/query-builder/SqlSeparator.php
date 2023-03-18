<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * séparateurs sql pris en charge
 */
enum SqlSeparator:string{
    case AND = "and";
    case OR = "or";
    case DESC = "desc";
    case ASC = "asc";
}