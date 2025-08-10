<?php

namespace Yahvya\EagleFramework\Utils\Sse;

/**
 * @brief Sse resource manager
 */
class ResourceManager
{
    /**
     * @var array Resources map
     */
    protected array $resources = [];

    /**
     * @brief Store a resource in the resource map
     * @param string|int $key Resource store key (you can override an already existing key-pair)
     * @param mixed $resource Resource content
     * @return $this
     */
    public function setResource(string|int $key, mixed $resource): ResourceManager
    {
        $this->resources[$key] = $resource;

        return $this;
    }

    /**
     * @brief Provided a stored resource value
     * @param string|int $key Associated key
     * @return mixed Resource value or null when not found
     * @attention If the expected value is null, you have to prevent this case
     */
    public function getResource(string|int $key): mixed
    {
        return $this->resources[$key] ?? null;
    }

    /**
     * @brief Clear the resources map
     * @return $this
     */
    public function clear(): ResourceManager
    {
        $this->resources = [];

        return $this;
    }
}
