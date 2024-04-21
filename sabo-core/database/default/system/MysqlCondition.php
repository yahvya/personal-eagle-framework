<?php

namespace SaboCore\Database\Default\System;

use Override;
use SaboCore\Database\System\DatabaseComparator;
use SaboCore\Database\System\DatabaseCondition;

/**
 * @brief Condition mysql
 * @author yahaya bathily https://github.com/yahvya
 */
class MysqlCondition extends DatabaseCondition{
    /**
     * @param string|MysqlFunction $condGetter Nom de l'attribut ou Fonction mysql
     * @param MysqlComparator $comparator Comparateur
     * @param mixed $conditionValue Valeur à vérifier
     * @attention En cas de getter sous forme de condition, veuillez ne pas fournir d'alias
     */
    public function __construct(mixed $condGetter, MysqlComparator $comparator, mixed $conditionValue){
        parent::__construct($condGetter, $comparator, $conditionValue);
    }

    /**
     * @return MysqlFunction|string le nom de l'attribut ou la fonction
     */
    #[Override]
    public function getCondGetter(): mixed{
        return $this->condGetter;
    }

    /**
     * @return MysqlComparator comparateur
     */
    #[Override]
    public function getComparator(): DatabaseComparator{
        return parent::getComparator();
    }
}