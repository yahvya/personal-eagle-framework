<?php

namespace Sabo\Utils\Routes;

use Closure;

/**
 * Route
 */
abstract class Route
{
    /**
     * @var Closure[] Route access conditions
     */
    protected array $accessConditions = [];

    /**
     * @param string $link Route link with dynamic params
     * @param string $requestMethod Request method
     * @param string $routeName route name
     * @param array|Closure $handler Execution handler
     */
    public function __construct(
        public string $link,
        public readonly string $requestMethod,
        public readonly string $routeName,
        public readonly array|Closure $handler
    )
    {}

    /**
     * Add a prefix link to the link
     * @param string $prefix Prefix link
     * @return $this
     */
    public function addPrefix(string $prefix):static
    {
        $this->link = "$prefix$this->link";
        return $this;
    }

    /**
     * Add access verifier
     * @param Closure $conditionVerifier Boolean closure to verify access. Can use dependency injection
     * @return $this
     */
    public function addAccessCondition(Closure $conditionVerifier):static
    {
        $this->accessConditions[] = $conditionVerifier;

        return $this;
    }

    /**
     * @return Closure[] access conditions
     */
    public function getAccessConditions():array
    {
        return $this->accessConditions;
    }
}