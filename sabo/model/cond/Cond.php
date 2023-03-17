<?php

namespace Sabo\Model\Cond;

/**
 * interface représentant une condition posé sur un attribut lié à une colonne en base de données
 */
interface Cond{
    /**
     * vérifie la condition
     * @param data la donnée à valider
     * @return bool si la validation a réussie
     */
    public function checkCondWith(mixed $data):bool;

    /**
     * @return string le message d'erreur en cas d'échec de validation de la condition
     */
    public function getErrorMessage():string;
}