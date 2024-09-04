<?php

namespace SaboCore\Routing\Request;

/**
 * @brief request manager
 */
readonly class Request{
    /**
     * @var string request uri
     */
    public string $requestUri;

    /**
     * @var string request method formated to lower string
     */
    public string $requestMethod;

    public function __construct(){
        $this->requestUri = $_SERVER["REQUEST_URI"];
        $this->requestMethod = strtolower(string: $_SERVER["REQUEST_METHOD"]);
    }
}