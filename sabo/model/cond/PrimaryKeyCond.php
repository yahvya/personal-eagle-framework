<?php

namespace Sabo\Model\Cond;

use Attribute;

/**
 * attribut définissant une clé primaire
 */
#[Attribute]
class PrimaryKeyCond implements Cond{

    /**
     * défini si la clé primaire est autoincrémenté
     */
    private bool $isAutoIncremented;

    /**
     * défini si la clé primaire peut être affecté même si auto incrémenté
     */
    private bool $canBeSetOnIncrement;

    /**
     * @param isAutoIncremented défini si la clé est auto incrémenté (par défaut à faux)
     */
    public function __construct(bool $isAutoIncremented = false,bool $canBeSetOnIncrement = true){
        $this->isAutoIncremented = $isAutoIncremented;
        $this->canBeSetOnIncrement = $canBeSetOnIncrement;
    }

    /**
     * @return bool si la clé est autoincrémenté
     */
    public function getIsAutoIncrmented():bool{
        return $this->isAutoIncremented;
    }

    public function checkCondWith(mixed $data):bool{
        return !$this->isAutoIncremented || $this->canBeSetOnIncrement;
    }

    public function getIsDisplayable():bool{
        return false;
    }

    public function getErrorMessage():string{
        return "La clé primaire ne peut être affecté";
    }
}