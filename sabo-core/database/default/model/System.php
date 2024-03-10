<?php

namespace SaboCore\Database\Default\Model;

use Exception;

/**
 * @brief interface représentant les méthodes à implémenter par le système
 * @author yahaya bathily https://github.com/yahvya
 */
interface System{
    /**
     * @bried Insère le model dans la base de données
     * @return bool si la requête a réussi
     */
    public function insert():bool;

    /**
     * @brief Supprime le model de la base de donnée
     * @return bool si la requête a réussi 
     */
    public function delete():bool;

    /**
     * @bried Supprime le model de la base de donnée
     * @return bool si la requête a réussi 
     */
    public function update():bool;

    /**
     * @brief Cherche des résultats en base de données à partir de conditions
     * @param array $conditions conditions à vérifier, format [attribute_name → value] ou [attribute_name → [value, SqlComparator, (non obligatoire and par défaut)] SqlSeparator and ou or], si vide alors alors select *
     * @param array $toSelect le nom des attributs liés aux colonnes à récupérer
     * @param bool $getBaseResult défini si les résultats doivent être retournés telles qu'elles ou sous forme d'objets
     * @return mixed un tableau contenant les objets si résultats multiples ou un objet model si un seul résultat ou PDOStatement de la requête si getBaseResult à true ou null si aucun résultat
     * @throws Exception (en mode debug) si données mal formulées
     */
    public static function find(array $conditions, array $toSelect = [], bool $getBaseResult = false):mixed;
}