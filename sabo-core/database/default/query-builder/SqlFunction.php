<?php

namespace SaboCore\Database\Default\QueryBuilder;

/**
 * @brief Liste des fonctions sql pris en charge
 * @attention peut être modifié
 * @author yahaya bathily https://github.com/yahvya
 */
enum SqlFunction:string{
    /**
     * @brief mysql count()
     */
    case COUNT = "count";

    /**
     * @brief mysql sum()
     */
    case SUM = "sum";

    /**
     * @brief mysql avg()
     */
    case AVG = "avg";

    /**
     * @brief mysql distinct()
     * @attention distinct * à mettre manuellement
     */
    case DISTINCT = "distinct";
}