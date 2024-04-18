<?php

namespace SaboCore\Database\Default\Conditions;

use SaboCore\Database\Default\System\MysqlModel;

/**
 * @brief Représente une condition de validation
 */
interface Cond{
    /**
     * @brief Vérifie si la donnée est valide
     * @param MysqlModel $baseModel Model de base
     * @param string $attributeName Nom de l'attribut
     * @param mixed $data donnée à vérifier
     * @return bool si la donnée est valide
     */
    public function verifyData(MysqlModel $baseModel,string $attributeName,mixed $data):bool;

    /**
     * @return string Le message d'erreur
     */
    public function getErrorMessage():string;

    /**
     * @return bool Si le message d'erreur peut être affiché
     */
    public function getIsDisplayable():bool;
}