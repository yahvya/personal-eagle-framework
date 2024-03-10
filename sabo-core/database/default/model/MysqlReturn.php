<?php

namespace SaboCore\Database\Default\Model;

/**
 * @brief Représente le type de la donnée à retourner
 * @author yahaya bathily https://github.com/yahvya
 */
enum MysqlReturn{
    /**
     * @brief null ou pdo statement
     */
    case DEFAULT;

    /**
     * @brief array de SaboModel ou SaboModel
     */
    case OBJECTS;

    /**
     * @brief true ou false
     */
    case SUCCESS_STATE;
}