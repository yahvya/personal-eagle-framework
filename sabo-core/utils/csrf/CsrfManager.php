<?php

namespace SaboCore\Utils\Csrf;


/**
 * @brief Gestionnaire de token csrf
 * @author yahaya bathily https://github.com/yahvya
 */
class CsrfManager{
    /**
     * @var string le token csrf
     */
    protected string $token;

    /**
     * @param string $token le token csrf
     */
    public function __construct(string $token){
        $this->token = $token;
    }

    /**
     * @return string le token csrf
     */
    public function getToken():string{
        return $this->token;
    }

    /**
     * @return string la version serializé
     */
    public function serialize():string{
        return serialize(value: $this);
    }

    /**
     * @param string $instance l'instance serializé
     * @return CsrfManager la version desérializé
     */
    public static function deserialize(string $instance):CsrfManager{
        return unserialize(data: $instance);
    }
}