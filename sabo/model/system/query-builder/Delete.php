<?php

namespace Sabo\Model\System\QueryBuilder;

/**
 * représente une requête delete
 */
trait Delete{

    /**
     * requête delete
     * @return QueryBuilder this
     */
    public function delete():QueryBuilder{
        $this->sqlString = "delete from {$this->linkedModel->getTableName()} as {$this->as} ";

        return $this;
    }

    /**
     * requete delete construis avec une clause where sur les clés primaires
     * @return QueryBuilder this
     * @throws Exception (en mode debug si aucune clé primaire trouvé)
     */
    public function deleteFromPrimaryKeys():QueryBuilder{
        $this->delete();

        return $this;
    }
}