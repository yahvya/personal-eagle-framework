<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * comparateur sql pris en charge
 */
enum SqlComparator:string{
    case LIKE = "like";
    case SUPERIOR = ">";
    case EQUAL = "=";
}