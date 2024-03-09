<?php

namespace SaboCore\Utils\Session;

use SaboCore\Utils\Csrf\CsrfManager;

/**
 * @brief Gestionnaire de session
 * @author yahaya bathily https://github.com/yahvya
 */
class SessionStorage{
    /**
     * @brief mappage des clés de stockage de la session
     */
    protected const array KEYMAP = [
        "forUser" => "USER",
        "forFlashDatas" => "FLASH",
        "forFramework" => "FRAMEWORK",
        "forCsrfToken" => "CSRF_TOKEN"
    ];

    /**
     * @brief Stock une donnée
     * @param string $storeKey clé de la donnée
     * @param mixed $toStore donnée
     * @return $this
     */
    public function store(string $storeKey,mixed $toStore):SessionStorage{
        $_SESSION[self::KEYMAP["forUser"] ][$storeKey] = $toStore;

        return $this;
    }

    /**
     * @brief Stock une donnée limité par le temps et nombre de rechargements de page
     * @param string $storeKey clé de la donnée
     * @param mixed $toStore donnée
     * @param int $countOfRedirectBefore nombre de redirections avant suppression
     * @param int $timeBeforeDelete temps de stockage de la donnée
     * @return $this
     */
    public function storeFlash(string $storeKey,mixed $toStore,int $countOfRedirectBefore = 1,int $timeBeforeDelete = 1800):SessionStorage{
        $_SESSION[self::KEYMAP["forFlashDatas"] ][$storeKey] = [
            "value" => $toStore,
            "config" => [
                "countOfRedirectBefore" => $countOfRedirectBefore,
                "timeBeforeDelete" => $timeBeforeDelete,
                "storeTime" => time()
            ]
        ];

        return $this;
    }

    /**
     * @brief Stock une donnée du framework
     * @param string $storeKey clé de la donnée
     * @param mixed $toStore donnée
     * @return $this
     */
    public function storeFramework(string $storeKey,mixed $toStore):SessionStorage{
        $_SESSION[self::KEYMAP["forFramework"] ][$storeKey] = $toStore;

        return $this;
    }

    /**
     * @param string $storeKey clé de stockage
     * @return mixed la valeur stockée ou null si non trouvé
     */
    public function getValue(string $storeKey):mixed{
        return $_SESSION[self::KEYMAP["forUser"]][$storeKey] ?? null;
    }

    /**
     * @param string $storeKey clé de stockage
     * @return mixed la valeur stockée ou null si non trouvé
     */
    public function getFrameworkValue(string $storeKey):mixed{
        return $_SESSION[self::KEYMAP["forFramework"]][$storeKey] ?? null;
    }

    /**
     * @param string $storeKey clé de stockage
     * @return mixed la valeur stockée ou null si non trouvé
     */
    public function getFlashValue(string $storeKey):mixed{
        return isset($_SESSION[self::KEYMAP["forFlashDatas"]][$storeKey]) ? $_SESSION[self::KEYMAP["forFlashDatas"]][$storeKey]["value"] : null;
    }

    /**
     * @brief Supprime une valeur en session
     * @param string $storeKey clé de stockage
     * @return $this
     */
    public function delete(string $storeKey):SessionStorage{
        unset($_SESSION[self::KEYMAP["forUser"] ][$storeKey]);

        return $this;
    }

    /**
     * @brief Supprime une valeur en session framework
     * @param string $storeKey clé de stockage
     * @return $this
     */
    public function deleteInFramework(string $storeKey):SessionStorage{
        unset($_SESSION[self::KEYMAP["forFramework"] ][$storeKey]);

        return $this;
    }

    /**
     * @brief Supprime une valeur en session flash
     * @param string $storeKey clé de stockage
     * @return $this
     */
    public function deleteInFlash(string $storeKey):SessionStorage{
        unset($_SESSION[self::KEYMAP["forFlashDatas"] ][$storeKey]);

        return $this;
    }

    /**
     * @brief Gère la durée de vie des données flash
     * @return $this
     */
    public function manageFlashDatas():SessionStorage{
        if(!isset($_SESSION[self::KEYMAP["forFlashDatas"] ]) ) $_SESSION[self::KEYMAP["forFlashDatas"] ] = [];

        foreach($_SESSION[self::KEYMAP["forFlashDatas"] ] as $key => $flashConfig){
            // vérification sur la durée et le temps d'expiration
            if(
                $flashConfig["config"]["countOfRedirectBefore"] === 0 ||
                time() - $flashConfig["config"]["storeTime"] >= $flashConfig["config"]["timeBeforeDelete"]
            ){
                unset($_SESSION[self::KEYMAP["forFlashDatas"] ][$key]);
                continue;
            }

            $flashConfig["config"]["countOfRedirectBefore"]--;

            $_SESSION[self::KEYMAP["forFlashDatas"] ][$key] = $flashConfig;
        }

        return $this;
    }

    /**
     * @brief Stock un token
     * @param CsrfManager $csrfManager le gestionnaire à stocker
     * @return $this
     */
    public function storeCsrf(CsrfManager $csrfManager):SessionStorage{
        $_SESSION[self::KEYMAP["forCsrfToken"] ][$csrfManager->getToken()] = $csrfManager->serialize();

        return $this;
    }

    /**
     * @param string $token le token csrf
     * @return CsrfManager|null le gestionnaire ou null
     */
    public function getCsrfFrom(string $token):CsrfManager|null{
        return isset($_SESSION[self::KEYMAP["forCsrfToken"]][$token]) ? CsrfManager::deserialize($_SESSION[self::KEYMAP["forCsrfToken"]][$token]) : null;
    }

    /**
     * @brief Supprime le token csrf
     * @param CsrfManager $csrfManager gestionnaire csrf à supprimer
     * @return $this
     */
    public function deleteCsrf(CsrfManager $csrfManager):SessionStorage{
        unset($_SESSION[self::KEYMAP["forCsrfToken"] ][$csrfManager->getToken()]);

        return $this;
    }

    /**
     * @return SessionStorage une nouvelle instance de SessionStorage
     */
    public static function create():SessionStorage{
        return new SessionStorage();
    }
}