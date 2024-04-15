<?php

namespace SaboCore\Database\Default\System;

use SaboCore\Database\System\DatabaseCondSeparator;

/**
 * @brief Séparateurs mysql
 * @author yahaya bathily https://github.com/yahvya
 */
class MysqlCondSeparator extends DatabaseCondSeparator{
    /**
     * @return MysqlCondSeparator Séparateur AND
     */
    public static function AND():MysqlCondSeparator{
        return new MysqlCondSeparator(separator: "AND");
    }

    /**
     * @return MysqlCondSeparator Séparateur OR
     */
    public static function OR():MysqlCondSeparator{
        return new MysqlCondSeparator(separator: "OR");
    }

    /**
     * @return MysqlCondSeparator Séparateur NOT
     */
    public static function NOT():MysqlCondSeparator{
        return new MysqlCondSeparator(separator: "NOT");
    }

    /**
     * @return MysqlCondSeparator Séparateur IS NULL
     */
    public static function IS_NULL():MysqlCondSeparator{
        return new MysqlCondSeparator(separator: "IS NULL");
    }

    /**
     * @return MysqlCondSeparator Séparateur IS NOT NULL
     */
    public static function IS_NOT_NULL():MysqlCondSeparator{
        return new MysqlCondSeparator(separator: "IS NOT NULL");
    }

    /**
     * @return MysqlCondSeparator Démarre un groupe de condition
     */
    public static function GROUP_START():MysqlCondSeparator{
        return new MysqlCondSeparator(separator: "(");
    }

    /**
     * @return MysqlCondSeparator Ferme un groupe de condition
     */
    public static function GROUP_END():MysqlCondSeparator{
        return new MysqlCondSeparator(separator: ")");
    }
}