<?php

namespace Sabo\Model\Attribute;

use Attribute;
use Sabo\Model\Cond\Cond;
use Sabo\Model\Cond\PrimaryKeyCond;

/**
 * attribut représentant une colonne en base de donnée
 */
#[Attribute]
class TableColumn{
    /**
     * nom de la colonne lié en base de donnée
     */
    private string $linkedColName;

    /**
     * liste des conditions à valider pour l'attribut
     */
    private array $conds;

    /**
     * défini si l'attribut est une clé primaire
     */
    private bool $isPrimaryKey;

    /**
     * défini si la clé primaire est auto-incrémenté
     */
    private bool $isAutoIncremented;

    private bool $isNullable;

    /**
     * @param linkedColName nom de la colonne lié en base de données
     * @param linkedConds paramètres multiples , liste des conditions liés à l'élement
     */
    public function __construct(string $linkedColName,bool $isNullable,Cond... $linkedConds){
        $this->linkedColName = $linkedColName;
        $this->conds = $linkedConds;
        $this->isNullable = $isNullable;
        $this->isPrimaryKey = false;
        $this->isAutoIncremented = false;

        foreach($this->conds as $cond){
            if($cond instanceof PrimaryKeyCond){
                $this->isPrimaryKey = true;
                $this->isAutoIncremented = $cond->getIsAutoIncrmented();

                break;
            }
        }
    }

    /**
     * vérifie si la donnée peut être affecté à l'attribut lié
     * @param data la donnée à valider
     * @return bool|Cond si la validation a réussie ou Cond la condition qui a echoué
     */
    public function canBeSetToAttribute(mixed $data):bool{
        foreach($this->conds as $cond){
            if(!$cond->checkCondWith($data) ) return $cond;
        }

        return true;
    }

    /**
     * @return array la liste des conditions
     */
    public function getConds():array{
        return $this->conds;
    }

    /**
     * @return bool si l'attribut est une clé primaire
     */
    public function getIsAutoIncremented():bool{
        return $this->isPrimaryKey && $this->isAutoIncremented;
    }

    /**
     * @return bool si l'attribut est une clé primaire auto incrémenté
     */
    public function getIsPrimaryKey():bool{
        return $this->isPrimaryKey;
    }

    /**
     * @return string le nom de la colonne lié
     */
    public function getLinkedColName():string{
        return $this->linkedColName;
    }

    /**
     * @return bool si l'élement est nullable
     */
    public function getIsNullable():bool{
        return $this->isNullable;
    }
}