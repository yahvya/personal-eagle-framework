<?php

namespace Sabo\Model\Attribute;

use \Attribute;
use Sabo\Model\Cond\Cond;
use Sabo\Model\Exception\ModelCondException;
use Sabo\Model\Model\SaboModel;

/**
 * attribut représentant une liaison avec une colonne 
 */
#[Attribute]
class JoinedColumn implements Cond{
    /**
     * class du model à charger
     */
    private string $linkedModelClass;

    /**
     * tableau indicé par le nom des attributs du model lié avec valeur nom d'attribut du model actuel
     */
    private array $linkedSelectors;

    /**
     * @param linkedModelClass class du model à charger
     * @param linkedSelectors tableau indicé par le nom des attributs du model lié avec valeur nom d'attribut du model actuel
     * @throws Exception si le model n'est pas un enfant de sabomodel
     */
    public function __construct(string $linkedModelClass,array $linkedSelectors){
        if(!is_subclass_of($linkedModelClass,SaboModel::class) ) throw new ModelCondException($this);

        $this->linkedModelClass = $linkedModelClass;
        $this->linkedSelectors = $linkedSelectors;
    }

    /**
     * @return string class du model à charger
     */
    public function getLinkedModelClass():string{
        return $this->linkedModelClass;
    }

    /**
     * @return array le tableau indicé par le nom des attributs du model lié avec valeur nom d'attribut du model actuel
     */
    public function getLinkedSelectors():array{
        return $this->linkedSelectors;
    }

    public function getErrorMessage():string{
        return "Liaison non valide";
    }

    public function checkCondWith(mixed $data):bool{
        return false;
    }

    public function getIsDisplayable():bool{
        return false;
    }
}