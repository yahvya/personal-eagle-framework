<?php

namespace SaboCore\Database\Default\Conditions;

use Attribute;
use Closure;
use Override;
use SaboCore\Database\Default\System\MysqlModel;

/**
 * @brief Représente une condition pouvant être appellé
 * @author yahaya bathily https://github.com/yahvya
 */
#[Attribute]
class CallableCond implements Cond{
    /**
     * @brief Le callable booléen à vérifier
     */
    private array|Closure $toVerify;

    /**
     * @brief Le message d'erreur
     */
    private string $errorMessage;

    /**
     * @brief Si l'erreur peut être affichée
     */
    private bool $isDisplayable;
    
    /**
     * @param callable $toVerify le callable à vérifier, doit renvoyer un booléen
     * @param string $errorMessage le message d'erreur
     * @param bool $isDisplayable défini si l'erreur peut être affichée
     */
    public function __construct(callable $toVerify,string $errorMessage,bool $isDisplayable){
        $this->toVerify = $toVerify;
        $this->errorMessage = $errorMessage;
        $this->isDisplayable = $isDisplayable;
    }

    #[Override]
    public function verifyData(MysqlModel $baseModel,string $attributeName,mixed $data):bool{
        return call_user_func(callback: $this->toVerify,args: $data);
    }

    #[Override]
    public function getIsDisplayable():bool{
        return $this->isDisplayable;
    }

    #[Override]
    public function getErrorMessage():string{
        return $this->errorMessage;
    }
}
