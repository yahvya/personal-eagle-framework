<?php

namespace SaboCore\Database\Default\QueryBuilder;

use Exception;

/**
 * @brief Représente une requête delete
 * @author yahaya bathily https://github.com/yahvya
 */
trait Delete{

    /**
     * @brief Requête delete
     * @return QueryBuilder this
     */
    public function delete():QueryBuilder{
        $this->sqlString = "DELETE FROM {$this->linkedModel->getTableName()} AS $this->as ";

        return $this;
    }

    /**
     * @brief Requête delete construit avec une clause where sur les clés primaires
     * @return QueryBuilder this
     * @throws Exception (en mode debug si aucune clé primaire trouvé)
     */
    public function deleteFromPrimaryKeys():QueryBuilder{
        $this->delete();

        return $this;
    }
}