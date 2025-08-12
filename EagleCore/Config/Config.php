<?php

namespace Yahvya\EagleFramework\Config;

/**
 * @brief Configuration
 */
class Config
{
    /**
     * @var array{string:mixed} Configuration
     */
    protected(set) array $config = [];

    /**
     * @brief Add / Update a configuration element
     * @param string|int $name Config key
     * @param mixed $value Associated value
     * @return $this
     */
    public function setConfig(string|int $name, mixed $value): Config
    {
        $this->config[$name] = $value;

        return $this;
    }

    /**
     * @brief Search a configuration
     * @param string|int $name Config key
     * @return mixed The configuration associated value
     * @throws ConfigException On error
     */
    public function getConfig(string|int $name): mixed
    {
        if (!array_key_exists(key: $name, array: $this->config))
            throw new ConfigException(message: "The configuration with the key <$name> was not found");

        return $this->config[$name];
    }

    /**
     * @brief Check that the provided configuration keys exist
     * @param string|int ...$keys Configuration keys
     * @return void
     * @throws ConfigException On error
     */
    public function checkConfigs(string|int ...$keys): void
    {
        foreach ($keys as $key)
        {
            if (!array_key_exists(key: $key, array: $this->config))
                throw new ConfigException(message: "Configuration with key <$key> was not found");
        }
    }

    /**
     * @brief Provide a new config instance
     * @return Config New configuration instance
     */
    public static function create(): Config
    {
        return new Config();
    }
}