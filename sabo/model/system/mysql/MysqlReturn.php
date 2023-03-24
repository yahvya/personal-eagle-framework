<?php

namespace Sabo\Model\System\Mysql;

/**
 * représente le type de la donnée à retourner
 */
enum MysqlReturn{
    case DEFAULT; // null ou pdoexception
    case OBJECTS; // array ou SaboModel
    case SUCCESS_STATE; // true ou false
}