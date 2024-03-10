<?php

namespace SaboCore\Database\Default\Cond;

use Attribute;
use Override;

/**
 * @brief Attribut définissant une clé primaire
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class PrimaryKeyCond implements Cond{

    /**
     * @brief Défini si la clé primaire est autoincrémenté
     */
    private bool $isAutoIncremented;

    /**
     * @brief Défini si la clé primaire peut être affecté même si auto incrémenté
     */
    private bool $canBeSetOnIncrement;

    /**
     * @param bool $isAutoIncremented défini si la clé est auto incrémenté (par défaut à faux)
     * @param bool $canBeSetOnIncrement si une valeur peut être setAttribute sur l'attribut malgré l'auto-increment
     */
    public function __construct(bool $isAutoIncremented = true,bool $canBeSetOnIncrement = true){
        $this->isAutoIncremented = $isAutoIncremented;
        $this->canBeSetOnIncrement = $canBeSetOnIncrement;
    }

    /**
     * @return bool si la clé est autoincrémenté
     */
    public function getIsAutoIncremented():bool{
        return $this->isAutoIncremented;
    }

    #[Override]
    public function checkCondWith(mixed $data):bool{
        return !$this->isAutoIncremented || $this->canBeSetOnIncrement;
    }

    #[Override]
    public function getIsDisplayable():bool{
        return false;
    }

    #[Override]
    public function getErrorMessage():string{
        return "La clé primaire ne peut être affecté";
    }
}