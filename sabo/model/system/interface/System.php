<?php

namespace Sabo\Model\System\Interface;

/**
 * interface représentant les méthodes à implémenter par un système (mysql,postgree???)
 */
interface System{
    /**
     * insère le model dans la base de données
     * @return bool si la requête a réussi
     */
    public function insert():bool;
    /**
     * supprime le model de la base de donnée
     * @return bool si la requête a réussi 
     */
    public function delete():bool;
    /**
     * supprime le model de la base de donnée
     * @return bool si la requête a réussi 
     */
    public function update():bool;

    /**
     * cherche des résultats en base de données à partir de conditions
     * @param conds conditions à vérifier, format [attribute_name => value] ou [attribute_name => [value,SqlComparator,(non obligatoire and par défaut)] SqlSeparator and ou or] , si conds est vide alors select *
     * @param toSelect le nom des attributs liés aux colonnes à récupérer
     * @param getBaseResult défini si les résultats doivent être retournés telles qu'elles ou sous forme d'objets
     * @return mixed un tableau contenant les objets si résultats multiples ou un objet model si un seul résultat ou pdostatement de la requête si getBaseResult à true ou null si aucun résultat
     * @throws Exception (en mode debug) si données mal formulés 
     */
    public static function find(array $conds,array $toSelect = [],bool $getBaseResult = false):mixed;

    /**
     * initialise les modèles
     * @return bool état de succès
     */
    public static function initModel():bool;
}