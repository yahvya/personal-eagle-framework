<?php

namespace SaboCore\Core\Http;

/**
 * Application request manager
 */
class RequestManager
{
    /**
     * @var string Request URL
     */
    public readonly string $url;

    public function __construct()
    {
        $this->url = parse_url(url: $_SERVER["REQUEST_URI"],component: PHP_URL_PATH);
    }

    /**
     * @return array GET params
     */
    public function params():array{
        return $_GET;
    }

    /**
     * @param string $getName GET param name
     * @return mixed GET param with the provided name or NULL when not found
     */
    public function param(string $getName):mixed{
        return $_GET[$getName] ?? null;
    }
}