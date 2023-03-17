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
     * @param conds conditions à vérifier
     * @param getBaseResult défini si les résultats doivent être retournés telles qu'elles ou sous forme d'objets
     * @return bool si la requête a réussi 
     */
    public static function find(array $conds,bool $getBaseResult = false):mixed;

    /**
     * initialise les modèles
     * @return bool état de succès
     */
    public static function initModel():bool;
}