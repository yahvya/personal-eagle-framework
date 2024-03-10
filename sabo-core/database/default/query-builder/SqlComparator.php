<?php

namespace SaboCore\Database\Default\QueryBuilder;

/**
 * @brief Comparateur sql pris en charge
 * @author yahaya bathily https://github.com/yahvya
 */
enum SqlComparator:string{
    /**
     * @brief Comparaison "like"
     */
    case LIKE = "like";

    /**
     * @brief Comparaison "supérieur"
     */
    case SUPERIOR = ">";

    /**
     * @bried Comparaison "inférieur"
     */
    case INFERIOR = "<";

    /**
     * @brief Comparaison "égal"
     */
    case EQUAL = "=";
}