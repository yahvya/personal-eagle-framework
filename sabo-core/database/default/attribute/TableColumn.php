<?php

namespace SaboCore\Database\Default\Attribute;

use Attribute;
use SaboCore\Database\Default\Cond\Cond;
use SaboCore\Database\Default\Cond\PrimaryKeyCond;

/**
 * @brief Attribut représentant une colonne en base de donnée
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class TableColumn{
    /**
     * @brief Nom de la colonne lié en base de donnée
     */
    private string $linkedColName;

    /**
     * @brief Liste des conditions à valider pour l'attribut
     */
    private array $conditions;

    /**
     * @brief Défini si l'attribut est une clé primaire
     */
    private bool $isPrimaryKey;

    /**
     * @brief Défini si la clé primaire est auto-incrémenté
     */
    private bool $isAutoIncremented;

    /**
     * @var bool @brief Si la colonne peut avoir une valeur null
     */
    private bool $isNullable;

    /**
     * @param string $linkedColName nom de la colonne lié en base de données
     * @param bool $isNullable si le champ peut être mis à null
     * @param Cond ...$linkedConditions paramètres multiples, liste des conditions liés à l'élément
     */
    public function __construct(string $linkedColName,bool $isNullable,Cond ...$linkedConditions){
        $this->linkedColName = $linkedColName;
        $this->conditions = $linkedConditions;
        $this->isNullable = $isNullable;
        $this->isPrimaryKey = false;
        $this->isAutoIncremented = false;

        foreach($this->conditions as $cond){
            if($cond instanceof PrimaryKeyCond){
                $this->isPrimaryKey = true;
                $this->isAutoIncremented = $cond->getIsAutoIncremented();

                break;
            }
        }
    }

    /**
     * @brief Vérifie si la donnée peut être affectée à l'attribut lié
     * @param mixed $data la donnée à valider
     * @return bool|Cond si la validation a réussi ou Cond la condition qui a échoué
     */
    public function canBeSetToAttribute(mixed $data):bool|Cond{
        foreach($this->conditions as $cond){
            if(!$cond->checkCondWith($data) ) return $cond;
        }

        return true;
    }

    /**
     * @return array la liste des conditions
     */
    public function getConditions():array{
        return $this->conditions;
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
     * @return bool si l'élément est nullable
     */
    public function getIsNullable():bool{
        return $this->isNullable;
    }
}