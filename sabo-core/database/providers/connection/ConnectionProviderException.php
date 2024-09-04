<?php

namespace SaboCore\Database\Providers\Connection;

use Exception;

/**
 * @brief connection provider exception format
 */
class ConnectionProviderException extends Exception {
    /**
     * @param string $errorMessage errorMessage
     * @param Exception $baseException from exception
     */
    public function __construct(string $errorMessage,public readonly Exception $baseException) {
        parent::__construct(message: $errorMessage);
    }
}