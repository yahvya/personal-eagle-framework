<?php

namespace SaboCore\Utils\Session;

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
        "forFramework" => "FRAMEWORK"
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
     * @brief Gère la durée de vie des données flash
     * @return $this
     */
    public function manageFlashDatas():SessionStorage{
        if(!isset($_SESSION[self::KEYMAP["forFlashDatas"] ]) ) $_SESSION[self::KEYMAP["forFlashDatas"] ] = [];

        foreach($_SESSION[self::KEYMAP["forFlashDatas"] ] as $key => $flashConfig){
            // vérification sur la durée et le temps d'expiration
            if(
                $flashConfig["countOfRedirectBefore"] === 0 ||
                time() - $flashConfig["storeTime"] >= $flashConfig["timeBeforeDelete"]
            )
                unset($_SESSION[self::KEYMAP["forFlashDatas"] ][$key]);
        }

        return $this;
    }
}