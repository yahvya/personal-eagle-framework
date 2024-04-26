<?php

namespace SaboCore\Database\System;

use SaboCore\Utils\List\SaboList;

/**
 * @brief Modèle de la base de données
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class DatabaseModel{
    /**
     * @brief Crée la ligne dans la base de données
     * @return bool Si la création réussie
     * @throws DatabaseActionException en cas d'erreur
     */
    public abstract function create():bool;

    /**
     * @brief Actions à faire après la création du model
     * @param mixed $datas Données à fournir
     * @attention il est recommandé d'appeler parent::afterCreate en cas de redéfinition
     * @return $this
     *@throws DatabaseActionException pour stopper l'action en cas d'erreur
     */
    protected function afterCreate(mixed $datas):DatabaseModel{
        return $this;
    }

    /**
     * @brief Actions à faire avant la création du model
     * @param mixed $datas Données à fournir
     * @attention il est recommandé d'appeler parent::beforeCreate en cas de redéfinition
     * @throws DatabaseActionException pour stopper l'action en cas d'erreur
     * @return $this
     */
    protected function beforeCreate(mixed $datas):DatabaseModel{
        return $this;
    }

    /**
     * @brief Met à jour la ligne dans la base de données
     * @return bool Si la mise à jour réussie
     * @throws DatabaseActionException en cas d'erreur
     */
    public abstract function update():bool;

    /**
     * @brief Actions à faire après la mise à jour du model
     * @param mixed $datas Données à fournir
     * @attention il est recommandé d'appeler parent::afterUpdate en cas de redéfinition
     * @throws DatabaseActionException pour stopper l'action en cas d'erreur
     * @return $this
     */
    protected function afterUpdate(mixed $datas):DatabaseModel{
        return $this;
    }

    /**
     * @brief Actions à faire avant la mise à jour du model
     * @param mixed $datas Données à fournir
     * @attention il est recommandé d'appeler parent::beforeUpdate en cas de redéfinition
     * @throws DatabaseActionException pour stopper l'action en cas d'erreur
     * @return $this
     */
    protected function beforeUpdate(mixed $datas):DatabaseModel{
        return $this;
    }

    /**
     * @brief Met à jour la ligne dans la base de données
     * @return bool Si la mise à jour réussie
     * @throws DatabaseActionException en cas d'erreur
     */
    public abstract function delete():bool;

    /**
     * @brief Actions à faire après la suppression du model
     * @param mixed $datas Données à fournir
     * @attention il est recommandé d'appeler parent::afterDelete en cas de redéfinition
     * @throws DatabaseActionException pour stopper l'action en cas d'erreur
     * @return $this
     */
    protected function afterDelete(mixed $datas):DatabaseModel{
        return $this;
    }

    /**
     * @brief Actions à faire avant la suppression du model
     * @param mixed $datas Données à fournir
     * @attention il est recommandé d'appeler parent::beforeDelete en cas de redéfinition
     * @throws DatabaseActionException pour stopper l'action en cas d'erreur
     * @return $this
     */
    protected function beforeDelete(mixed $datas):DatabaseModel{
        return $this;
    }

    /**
     * @brief Actions à faire avant la génération du model à partir de la méthode find
     * @param mixed $datas Données à fournir
     * @attention il est recommandé d'appeler parent::beforeGeneration en cas de redéfinition
     * @throws DatabaseActionException pour stopper l'action en cas d'erreur
     * @return $this
     */
    protected function beforeGeneration(mixed $datas):DatabaseModel{
        return $this;
    }

    /**
     * @brief Actions à faire après la génération du model à partir de la méthode find
     * @param mixed $datas Données à fournir
     * @attention il est recommandé d'appeler parent::afterGeneration en cas de redéfinition
     * @throws DatabaseActionException pour stopper l'action en cas d'erreur
     * @return $this
     */
    protected function afterGeneration(mixed $datas):DatabaseModel{
        return $this;
    }

    /**
     * @brief Recherche une occurrence
     * @param DatabaseCondition ...$findBuilders Configuration de recherche
     * @return DatabaseModel|null le modèle trouvé ou null
     */
    public abstract static function findOne(DatabaseCondition|DatabaseCondSeparator ...$findBuilders):DatabaseModel|null;

    /**
     * @brief Recherche toutes les occurrences
     * @param DatabaseCondition ...$findBuilders Configuration de recherche
     * @return SaboList<DatabaseModel> liste des occurrences
     */
    public abstract static function findAll(DatabaseCondition|DatabaseCondSeparator ...$findBuilders):SaboList;
}