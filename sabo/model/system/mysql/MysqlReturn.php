<?php

namespace Sabo\Model\System\Mysql;

/**
 * représente le type de la donnée à retourner
 */
enum MysqlReturn{
    case DEFAULT; // null ou pdo statement
    case OBJECTS; // array de SaboModel ou SaboModel
    case SUCCESS_STATE; // true ou false
}