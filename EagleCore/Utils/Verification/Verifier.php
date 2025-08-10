<?php

namespace Yahvya\EagleFramework\Utils\Verification;

use Closure;

/**
 * @brief Verifier handler
 */
class Verifier
{
    /**
     * @param array|Closure $verifier Verification condition callable (with a boolean return)
     * @param array|Closure|null $onFailure Failure handler
     * @param array|Closure|null $onSuccess Success handler
     */
    public function __construct(
        protected array|Closure      $verifier,
        protected array|Closure|null $onFailure = null,
        protected array|Closure|null $onSuccess = null
    )
    {
    }

    /**
     * @brief Execute the verifier and provide the result
     * @param array $verifierArgs Parameters to send to the verifier
     * @return bool Verification result
     */
    public function verify(array $verifierArgs): bool
    {
        return call_user_func_array(callback: $this->verifier, args: $verifierArgs);
    }

    /**
     * @brief Execute the verification process by executing the verifier, then applying the success and failure handlers
     * @param array $verifierArgs Verifier handler parameters
     * @param array $onSuccessArgs Success handler parameters
     * @param array $onFailureArgs Failure handler parameters
     * @return array{string:mixed} Verification result ["success" → ...] or ["failure" → ...] or ["verifier" → ...]
     */
    public function execVerification(array $verifierArgs, array $onSuccessArgs = [], array $onFailureArgs = []): array
    {
        $verificationResult = $this->verify(verifierArgs: $verifierArgs);

        if ($verificationResult && $this->onSuccess !== null)
            return ["success" => call_user_func_array(callback: $this->onSuccess, args: $onSuccessArgs)];
        elseif (!$verificationResult && $this->onFailure !== null)
            return ["failure" => call_user_func_array(callback: $this->onFailure, args: $onFailureArgs)];

        return ["verifier" => $verificationResult];
    }
}