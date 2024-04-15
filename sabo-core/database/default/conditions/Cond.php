<?php

namespace SaboCore\Database\Default\Conditions;

/**
 * @brief Représente une condition de validation
 */
interface Cond{
    /**
     * @brief Vérifie si la donnée est valide
     * @param mixed $data donnée à vérifier
     * @return bool si la donnée est valide
     */
    public function verifyData(mixed $data):bool;

    /**
     * @return string Le message d'erreur
     */
    public function getErrorMessage():string;

    /**
     * @return bool Si le message d'erreur peut être affiché
     */
    public function getIsDisplayable():bool;
}