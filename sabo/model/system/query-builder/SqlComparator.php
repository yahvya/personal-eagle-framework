<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * comparateur sql pris en charge
 */
enum SqlComparator:string{
    /**
     * comparaison like
     */
    case LIKE = "like";
    /**
     * comparaison supérieur
     */
    case SUPERIOR = ">";
    /**
     * comparaison inférieur
     */
    case INFERIOR = "<";
    /**
     * comparaison égal
     */
    case EQUAL = "=";
}