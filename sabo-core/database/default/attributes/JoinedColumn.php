<?php

namespace SaboCore\Database\Default\Attributes;

use Attribute;

/**
 * @brief Attribut de liaison de tables
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class JoinedColumn{
    /**
     * @var string Model de la class
     */
    protected string $classModel;

    /**
     * @var array Configuration de jointure, indicé par les valeurs de la table actuelle et ayant comme valeur l'attribut de la table lié
     */
    protected array $joinConfig;

    /**
     * @var bool Si les données doivent être chargées à la génération du model
     */
    protected bool $loadOnGeneration;

    /**
     * @param string $classModel Model la class
     * @param array $joinConfig Configuration de jointure, indicé par les valeurs de la table actuelle et ayant comme valeur l'attribut de la table lié
     * @param bool $loadOnGeneration Si les données doivent être chargées à la génération du model. Si false la méthode "loadContent" devra être appellé sur la JoinedList avant utilisation
     */
    public function __construct(string $classModel,array $joinConfig,bool $loadOnGeneration = true){
        $this->classModel = $classModel;
        $this->joinConfig = $joinConfig;
        $this->loadOnGeneration = $loadOnGeneration;
    }

    /**
     * @return string Model la class
     */
    public function getClassModel():string{
        return $this->classModel;
    }

    /**
     * @return array Configuration de jointure, indicé par les valeurs de la table actuelle et ayant comme valeur l'attribut de la table lié
     */
    public function getJoinConfig():array{
        return $this->joinConfig;
    }

    /**
     * @return bool Si les données doivent être chargées à la génération du model
     */
    public function getLoadOnGeneration():bool{
        return $this->loadOnGeneration;
    }
}