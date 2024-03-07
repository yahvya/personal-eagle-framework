<?php

namespace SaboCore\Config;

/**
 * @brief Représente une configuration
 * @author yahaya bathily https://github.com/yahvya/
 */
class Config{
    /**
     * @var array configuration
     */
    private array $config = [];

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
        if(!array_key_exists($name,$this->config) ) throw new ConfigException("La configuration <$name> n'a pas été trouvé");

        return $this->config[$name];
    }

    /**
     * @brief Crée une nouvelle configuration
     * @return Config une nouvelle configuration
     */
    public static function create():Config{
        return new Config();
    }
}