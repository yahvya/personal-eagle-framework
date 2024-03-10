<?php

namespace SaboCore\Database\Default\Attribute;

use Attribute;
use Exception;
use Override;
use SaboCore\Database\Default\Cond\Cond;
use SaboCore\Database\Default\Exception\ModelCondException;
use SaboCore\Database\Default\Model\SaboModel;

/**
 * @brief Attribut représentant une liaison avec une colonne
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class JoinedColumn implements Cond{
    /**
     * @brief Class du model à charger
     */
    private string $linkedModelClass;

    /**
     * @brief Tableau indicé par le nom des attributs du model lié avec valeur nom d'attribut du model actuel
     */
    private array $linkedSelectors;

    /**
     * @param string $linkedModelClass class du model à charger
     * @param array $linkedSelectors tableau indicé par le nom des attributs du model lié avec valeur nom d'attribut du model actuel
     * @throws Exception si le model n'est pas un enfant de SaboModel
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

    #[Override]
    public function getErrorMessage():string{
        return "Liaison non valide";
    }

    #[Override]
    public function checkCondWith(mixed $data):bool{
        return false;
    }

    #[Override]
    public function getIsDisplayable():bool{
        return false;
    }
}