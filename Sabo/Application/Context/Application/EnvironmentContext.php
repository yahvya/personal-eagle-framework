<?php

namespace Sabo\Application\Context\Application;

/**
 * Env context
 */
class EnvironmentContext
{
    /**
     * @var array{string:mixed} Environment vars configuration
     */
    public array $environmentConfiguration = [];

    /**
     * Set a configuration
     * @param string $key Configuration key
     * @param mixed $value Configuration associated value
     * @return $this
     */
    public function set(string $key, mixed $value): EnvironmentContext
    {
        $this->environmentConfiguration[$key] = $value;
        return $this;
    }

    /**
     * Provide a configuration
     * @param string $key Configuration key
     * @return mixed The configuration associated value or null
     */
    public function get(string $key): mixed
    {
        return $this->environmentConfiguration[$key] ?? null;
    }

    /**
     * Remove a configuration
     * @param string $key Configuration key
     * @return $this
     */
    public function remove(string $key):EnvironmentContext
    {
        unset($this->environmentConfiguration[$key]);
        return $this;
    }
}