<?php

namespace SaboCore\Routing\Request;

use SaboCore\Utils\CustomTypes\Map;

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

    /**
     * @var Map<string|int|array|boolean> get values
     */
    public Map $getValues;

    /**
     * @var Map<string|int|array|boolean> post values
     */
    public Map $postValues;

    /**
     * @var Map<string|int|array|boolean> cookies
     */
    public Map $cookies;

    /**
     * @var Map<string|int|array|boolean> received files
     */
    public Map $receivedFiles;

    /**
     * @var string|null php://input
     */
    public ?string $phpInput;

    /**
     * @var Map headers
     */
    public Map $headers;

    /**
     * @var SessionManager session manager
     */
    public SessionManager $sessionManager;

    /**
     * @var UriMatcher request uri matcher
     */
    public UriMatcher $uriMatcher;

    public function __construct(){
        $this->requestUri = $this->loadRequestUri();
        $this->requestMethod = strtolower(string: $_SERVER["REQUEST_METHOD"]);
        $this->getValues = new Map(map: $_GET);
        $this->postValues = new Map(map: $_POST);
        $this->cookies = new Map(map: $_COOKIE);
        $this->receivedFiles = new Map(map: $_FILES);
        $this->sessionManager = new SessionManager();
        $this->uriMatcher = new UriMatcher(baseUri: $this->requestUri);

        $phpInput = @file_get_contents(filename: "php://input");
        $this->phpInput = $phpInput === false ? null : $phpInput;
    }

    /**
     * @return string the formated request uri
     */
    protected function loadRequestUri():string{
        $uri = @parse_url(url: $_SERVER["REQUEST_URI"])["path"] ?? "/";

        return str_ends_with(haystack: $uri,needle: "/") ? $uri : "$uri/";
    }
}