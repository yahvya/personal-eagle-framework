<?php

namespace SaboCore\Utils\Sse;

/**
 * @brief Gestinonaire de ressource
 * @author yahaya bathily https://github.com/yahvya
 */
class ResourceManager{
    /**
     * @var array ressources
     */
    protected array $resources = [];

    /**
     * @brief Stock une ressource
     * @param string|int $key clé de mappage de la ressource
     * @param mixed $ressource la ressource
     * @return $this
     */
    public function setRessource(string|int $key,mixed $resource):ResourceManager{
        $this->resources[$key] = $resource;

        return $this;
    }

    /**
     * @brief Fourni une ressource
     * @param string|int $key clé de mappage de la ressource
     * @return mixed la ressource ou null
     */
    public function getResource(string|int $key):mixed{
        return $this->resources[$key] ?? null;
    }

    /**
     * @brief Vide les ressources 
     * @return $this
     */
    public function clear():ResourceManager{
        $this->resources = [];
        
        return $this;
    }
}
