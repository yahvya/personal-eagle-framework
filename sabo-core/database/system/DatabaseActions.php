<?php

namespace SaboCore\Database\System;

/**
 * @brief Actions de la base de données
 * @author yahaya bathily https://github.com/yahvya
 */
enum DatabaseActions:int{
    /**
     * @brief Création du model en base de données
     */
    case MODEL_CREATE = 1;

    /**
     * @brief Actions avant création du model en base de données
     */
    case BEFORE_MODEL_CREATE = 2;

    /**
     * @brief Actions après création du model en base de données
     */
    case AFTER_MODEL_CREATE = 3;

    /**
     * @brief Mise à jour du model en base de données
     */
    case MODEL_UPDATE = 4;

    /**
     * @brief Actions avant mise à jour du model en base de données
     */
    case BEFORE_MODEL_UPDATE = 5;

    /**
     * @brief Actions après mise à jour du model en base de données
     */
    case AFTER_MODEL_UPDATE = 6;

    /**
     * @brief Suppression du model en base de données
     */
    case MODEL_DELETE = 7;

    /**
     * @brief Actions avant suppression du model en base de données
     */
    case BEFORE_MODEL_DELETE = 8;

    /**
     * @brief Actions après suppression du model en base de données
     */
    case AFTER_MODEL_DELETE = 9;

    /**
     * @brief Durant la génération du model
     */
    case ON_GENERATION = 10;

    /**
     * @brief Après génération du model lors du find
     */
    case AFTER_GENERATION = 11;

    /**
     * @brief Avant génération du model lors du find
     */
    case BEFORE_GENERATION = 12;
}
