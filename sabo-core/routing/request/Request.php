<?php

namespace SaboCore\Routing\Request;

use SaboCore\Utils\Session\SessionStorage;

/**
 * @brief Gestionnaire des données de la requête
 * @author yahaya bathily https://github.com/yahvya
 */
class Request{
    /**
     * @var SessionStorage gestionnaire de stockage de la session
     */
    protected SessionStorage $sessionStorage;

    public function __construct(){
        $this->sessionStorage = SessionStorage::create();
    }

    /**
     * @return SessionStorage le gestionnaire de stockage de la session
     */
    public function getSessionStorage():SessionStorage{
        return $this->sessionStorage;
    }
}