<?php

namespace SaboCore\Database\Default\Cond;

/**
 * @brief Interface représentant une condition posé sur un attribut lié à une colonne en base de données
 * @author yahaya bathily https://github.com/yahvya
 */
interface Cond{
    /**
     * @brief Vérifie la condition
     * @param mixed $data la donnée à valider
     * @return bool si la validation a réussie
     */
    public function checkCondWith(mixed $data):bool;

    /**
     * @return bool si l'erreur peut être envoyée à l'utilisateur
     */
    public function getIsDisplayable():bool;

    /**
     * @return string le message d'erreur en cas d'échec de validation de la condition
     */
    public function getErrorMessage():string;
}