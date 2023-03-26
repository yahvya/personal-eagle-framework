<?php

namespace Sabo\Model\System\Mysql;

/**
 * représente le type de la donnée à retourner
 */
enum MysqlReturn{
    /**
     * null ou pdo statement
     */
    case DEFAULT;
    /**
     * array de SaboModel ou SaboModel
     */
    case OBJECTS;
    /**
     * true ou false
     */
    case SUCCESS_STATE;
}