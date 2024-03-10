<?php

namespace SaboCore\Database\Default\QueryBuilder;

/**
 * @brief Séparateurs sql pris en charge
 * @author yahaya bathily https://github.com/yahvya
 */
enum SqlSeparator:string{
    /**
     * @brief Comparaison and
     */
    case AND = "and";

    /**
     * @brief Comparaison or
     */
    case OR = "or";

    /**
     * @brief order by desc
     */
    case DESC = "desc";

    /**
     * @brief order by asc
     */
    case ASC = "asc";
}