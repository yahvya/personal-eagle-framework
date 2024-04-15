<?php

namespace SaboCore\Utils\Verification;

use Closure;

/**
 * @brief Gestionnaire de vérification
 * @author yahaya bathily https://github.com/yahvya
 */
class Verifier{
    /**
     * @var array|Closure condition de vérification à retour booléen
     */
    protected array|Closure $verifier;

    /**
     * @var array|Closure|null action à faire en cas d'échec
     */
    protected array|Closure|null $onFailure;

    /**
     * @var array|Closure|null action à faire en cas de succès
     */
    protected array|Closure|null $onSuccess;

    /**
     * @param callable $verifier condition de vérification à retour booléen
     * @param callable|null $onFailure action à faire en cas d'échec
     * @param callable|null $onSuccess action à faire en cas de succès
     */
    public function __construct(Callable $verifier,Callable|null $onFailure = null, ?Callable $onSuccess = null){
        $this->verifier = $verifier;
        $this->onFailure = $onFailure;
        $this->onSuccess = $onSuccess;
    }

    /**
     * @brief Exécute la condition de vérification et fourni son résultat
     * @param array $verifierArgs paramètres à envoyer à la fonction de vérification
     * @return bool le résultat de la vérification
     */
    public function verify(array $verifierArgs):bool{
        return call_user_func_array(callback: $this->verifier,args: $verifierArgs);
    }

    /**
     * @brief Exécute le processus de vérification en exécutant les fonctions success ou failure
     * @param array $verifierArgs paramètres à envoyer à la fonction de vérification
     * @param array $onSuccessArgs arguments à envoyer à la fonction de succès
     * @param array $onFailureArgs arguments à envoyer à la fonction d'échec
     * @return array le résultat de la vérification ["success" → ...] ou ["failure" → ...] ou ["verifier" → ...]
     */
    public function execVerification(array $verifierArgs,array $onSuccessArgs = [],array $onFailureArgs = []):array{
        $verificationResult = $this->verify(verifierArgs: $verifierArgs);

        if($verificationResult && $this->onSuccess !== null)
            return ["success" => call_user_func_array(callback: $this->onSuccess,args: $onSuccessArgs)];
        elseif(!$verificationResult && $this->onFailure !== null)
            return ["failure" => call_user_func_array(callback: $this->onFailure,args: $onFailureArgs)];

        return ["verifier" => $verificationResult];
    }
}