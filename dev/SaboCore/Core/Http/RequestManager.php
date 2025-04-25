<?php

namespace SaboCore\Core\Http;

use SaboCore\Core\Mappers\Implementation\ArrayDtoMapper;

/**
 * Application request manager
 */
readonly class RequestManager
{
    /**
     * @var string Request URL
     */
    public string $url;

    /**
     * @var string Request method
     */
    public string $method;

    public function __construct()
    {
        $this->url = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * @return array GET params
     */
    public function params(): array
    {
        return $_GET;
    }

    /**
     * @param string $getName GET param name
     * @return mixed GET param with the provided name or NULL when not found
     */
    public function param(string $getName): mixed
    {
        return $_GET[$getName] ?? null;
    }

    /**
     * @return array POST params
     */
    public function postValues(): array
    {
        return $_POST;
    }

    /**
     * @param string $postName Post param name
     * @return mixed POST param with the provided name or NULL when not found
     */
    public function postValue(string $postName): mixed
    {
        return $_POST[$postName] ?? null;
    }

    /**
     * @return array COOKIES params
     */
    public function cookiesValues(): array
    {
        return $_COOKIE;
    }

    /**
     * @param string $cookieName Cookie param name
     * @return mixed Cookie param with the provided name or NULL when not found
     */
    public function cookieValue(string $cookieName): mixed
    {
        return $_COOKIE[$cookieName] ?? null;
    }

    /**
     * Map COOKIES params in the provided dto class
     * @param string $dto Dto class
     * @return object|null Dto instance or null on failure
     */
    public function mapCookiesIn(string $dto):object|null
    {
        return new ArrayDtoMapper()->map(data: $_COOKIE,in: $dto);
    }

    /**
     * Retrieve raw input for methods like PUT, PATCH, DELETE
     * @return array|string|null Parsed array if JSON, raw string otherwise
     */
    public function input(): array|string|null
    {
        $content = file_get_contents(filename: 'php://input');
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains(haystack: $contentType,needle: 'application/json'))
        {
            $decoded = json_decode(json: $content,associative: true);
            return is_array(value: $decoded) ? $decoded : null;
        }

        return $content;
    }

    /**
     * Get all request data according to the HTTP method
     * @return array|string|null
     */
    public function data(): array|string|null
    {
        return match ($this->method) {
            'GET' => $this->params(),
            'POST' => $this->postValues(),
            'PUT', 'PATCH', 'DELETE' => $this->input(),
            default => null,
        };
    }

    /**
     * Map GET params in the provided dto class
     * @param string $dto Dto class
     * @return object|null Dto instance or null on failure
     */
    public function mapParamsIn(string $dto): object|null
    {
        return new ArrayDtoMapper()->map(data: $this->params(), in: $dto);
    }

    /**
     * Map POST params in the provided dto class
     * @param string $dto Dto class
     * @return object|null Dto instance or null on failure
     */
    public function mapPostValuesIn(string $dto): object|null
    {
        return new ArrayDtoMapper()->map(data: $this->postValues(), in: $dto);
    }

    /**
     * Map input data (json or raw) in the provided dto class
     * @param string $dto Dto class
     * @return object|null
     */
    public function mapInputIn(string $dto): object|null
    {
        $data = $this->input();
        return is_array(value: $data) ? new ArrayDtoMapper()->map(data: $data, in: $dto) : null;
    }

    /**
     * Map all request data depending on HTTP method
     * @param string $dto
     * @return object|null
     */
    public function mapDataIn(string $dto): object|null
    {
        $data = $this->data();
        return is_array(value: $data) ? new ArrayDtoMapper()->map(data: $data, in: $dto) : null;
    }

    /**
     * @return array Request headers
     */
    public function headers(): array
    {
        if (function_exists(function: 'getallheaders'))
            return getallheaders();

        $headers = [];

        foreach ($_SERVER as $key => $value)
        {
            if (str_starts_with(haystack: $key, needle: 'HTTP_'))
            {
                $header = str_replace(search: '_',replace: '-',subject: strtolower(string: substr(string: $key,offset: 5)));
                $headers[ucwords(string: $header, separators: '-')] = $value;
            }
        }

        return $headers;
    }

    /**
     * @param string $headerName Header name
     * @return string|null Header value with the provided name or NULL when not found
     */
    public function headerValue(string $headerName): string|null
    {
        return array_find(
            array: $this->headers(),
            callback: fn($key) => strcasecmp(string1: $key,string2: $headerName) === 0
        );
    }

    /**
     * Map HEADERS in the provided dto class
     * @param string $dto Dto class
     * @return object|null Dto instance or null on failure
     */
    public function mapHeadersIn(string $dto): object|null
    {
        return new ArrayDtoMapper()->map(data: $this->headers(), in: $dto);
    }

}
