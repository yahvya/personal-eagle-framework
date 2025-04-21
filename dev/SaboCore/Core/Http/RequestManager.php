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

    public function __construct()
    {
        $this->url = parse_url(url: $_SERVER["REQUEST_URI"],component: PHP_URL_PATH);
    }

    /**
     * @return array GET params
     */
    public function params():array
    {
        return $_GET;
    }

    /**
     * @param string $getName GET param name
     * @return mixed GET param with the provided name or NULL when not found
     */
    public function param(string $getName):mixed
    {
        return $_GET[$getName] ?? null;
    }

    /**
     * Map params in the provided dto class
     * @param string $dto Dto class
     * @return object|null Dto instance or null on failure
     */
    public function mapParamsIn(string $dto):object|null
    {
        return new ArrayDtoMapper()->map(data: $_GET,in: $dto);
    }

    /**
     * @return array POST params
     */
    public function postValues():array
    {
        return $_POST;
    }

    /**
     * @param string $postName PARAM param name
     * @return mixed POST param with the provided name or NULL when not found
     */
    public function postValue(string $postName):mixed
    {
        return $_POST[$postName] ?? null;
    }

    /**
     * Map post values in the provided dto class
     * @param string $dto Dto class
     * @return object|null Dto instance or null on failure
     */
    public function mapPostValuesIn(string $dto):object|null
    {
        return new ArrayDtoMapper()->map(data: $_POST,in: $dto);
    }
}