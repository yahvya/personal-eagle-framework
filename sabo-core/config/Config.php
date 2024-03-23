<?php

namespace SaboCore\Config;

/**
 * @brief Représente une configuration
 * @author yahaya bathily https://github.com/yahvya/
 */
class Config{
    /**
     * @var array<string,mixed> configuration
     */
    protected array $config = [];

    /**
     * @brief Ajoute / Modifie un élément de configuration
     * @param string|int $name clé de configuration
     * @param mixed $value valeur associée
     * @return $this
     */
    public function setConfig(string|int $name, mixed $value):Config{
        $this->config[$name] = $value;

        return $this;
    }

    /**
     * @brief Recherche la configuration
     * @param string|int $name nom de la configuration recherchée
     * @return mixed la valeur associée
     * @throws ConfigException en cas de configuration non trouvée
     */
    public function getConfig(string|int $name):mixed{
        if(!array_key_exists(key: $name,array: $this->config) )
            throw new ConfigException(message: "La configuration <$name> n'a pas été trouvé");

        return $this->config[$name];
    }

    /**
     * @brief Vérifie que les configurations fournies existent
     * @param string|int ...$keys nom des configurations
     * @return void
     * @throws ConfigException en cas de clé nom trouvée
     */
    public function checkConfigs(string|int ...$keys):void{
        foreach($keys as $key){
            if(!array_key_exists(key: $key,array: $this->config) )
                throw new ConfigException(message: "Configuration <$key> non trouvée");
        }
    }

    /**
     * @brief Crée une nouvelle configuration
     * @return Config une nouvelle configuration
     */
    public static function create():Config{
        return new Config();
    }
}