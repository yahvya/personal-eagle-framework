<?php

namespace SaboCore\Database\Default\Conditions;

use Override;
use SaboCore\Database\Default\Conditions\Cond;
use SaboCore\Database\Default\System\MysqlModel;
use Throwable;

/**
 * @brief Vérifie la possibilité d'utiliser la donnée
 * @author yahaya bathily https://github.com/yahvya
 */
class EnumValidityCond implements Cond{
    /**
     * @var string Message d'erreur
     */
    protected string $errorMessage;

    /**
     * @var bool Si le message peut être affiché
     */
    protected bool $isDisplayable;

    /**
     * @param string $errorMessage Message d'erreur en cas de non validité
     */
    public function __construct(string $errorMessage = "Valeur invalide",bool $isDisplayable = true){
        $this->errorMessage = $errorMessage;
        $this->isDisplayable = $isDisplayable;
    }

    #[Override]
    public function verifyData(MysqlModel $baseModel,string $attributeName,mixed $data): bool{
        try{
            // récupération du tableau des valeurs possibles
            $possibleValues = $baseModel->getColumnConfig(attributName: $attributeName)->getPossibleValues()->toArray();
            return
                in_array(needle: $data,haystack: $possibleValues) ||
                is_numeric(value: $data) && array_key_exists(key: $data,array: $possibleValues);
        }
        catch(Throwable){
            return false;
        }
    }

    #[Override]
    public function getErrorMessage(): string{
        return $this->errorMessage;
    }


    public function getIsDisplayable(): bool{
        return $this->isDisplayable;
    }
}
