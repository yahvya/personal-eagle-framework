<?php

namespace SaboCore\Routing\Request;

use SaboCore\Utils\CustomTypes\Map;

/**
 * @brief session manager
 */
readonly class SessionManager{
    /**
     * @var Map session map
     */
    protected Map $session;

    /**
     * @var Map user session data's
     * @attention this map set function can't persist data's in session
     */
    public Map $userSession;

    public function __construct(){
        $this->session = new Map(map: $_SESSION);
        $this->userSession = new Map(map: $_SESSION[SessionStorageMapping::USER_STORAGE->value] ?? []);
    }

    /**
     * @brief store a data
     * @param string $storeKey data key
     * @param mixed $toStore data to store
     * @return $this
     */
    public function store(string $storeKey,mixed $toStore):static{
        $_SESSION[SessionStorageMapping::USER_STORAGE->value][$storeKey] = $toStore;

        return $this;
    }

    /**
     * @brief store a life limited data. The limitation is made by the count of page refreshing
     * @param string $storeKey data key
     * @param mixed $toStore data to store
     * @param int $countOfRedirectBefore count of redirection
     * @param int $timeBeforeDelete storage time
     * @return $this
     */
    public function storeFlash(string $storeKey,mixed $toStore,int $countOfRedirectBefore = 1,int $timeBeforeDelete = 1800):static{
        $_SESSION[SessionStorageMapping::FLASH_DATA->value][$storeKey] = [
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
     * @brief store a framework data Stock une donnée du framework
     * @param FrameworkStorageMapping $storeKey store key
     * @param mixed $toStore data
     * @return $this
     */
    public function storeFramework(FrameworkStorageMapping $storeKey,mixed $toStore):static{
        $_SESSION[SessionStorageMapping::FRAMEWORK_STORAGE->value][$storeKey->value] = $toStore;

        return $this;
    }

    /**
     * @param string $storeKey storage key
     * @return mixed stored data or null on not found
     */
    public function getValue(string $storeKey):mixed{
        return $_SESSION[SessionStorageMapping::USER_STORAGE->value][$storeKey] ?? null;
    }

    /**
     * @param FrameworkStorageMapping $storeKey storage key
     * @return mixed the stored data or null on not found
     */
    public function getFrameworkValue(FrameworkStorageMapping $storeKey):mixed{
        return $_SESSION[SessionStorageMapping::FRAMEWORK_STORAGE->value][$storeKey->value] ?? null;
    }

    /**
     * @param string $storeKey storage key
     * @return mixed the stored data or null on not found
     */
    public function getFlashValue(string $storeKey):mixed{
        return isset($_SESSION[SessionStorageMapping::FLASH_DATA->value][$storeKey]) ?
            $_SESSION[SessionStorageMapping::FLASH_DATA->value][$storeKey]["value"] :
            null;
    }

    /**
     * @brief remove a session value
     * @param string $storeKey stored key
     * @return $this
     */
    public function delete(string $storeKey):static{
        unset($_SESSION[SessionStorageMapping::USER_STORAGE->value][$storeKey]);

        return $this;
    }

    /**
     * @brief delete a value in the session framework
     * @param FrameworkStorageMapping $storeKey storage key
     * @return $this
     */
    public function deleteInFramework(FrameworkStorageMapping $storeKey):static{
        unset($_SESSION[SessionStorageMapping::FRAMEWORK_STORAGE->value][$storeKey->value]);

        return $this;
    }

    /**
     * @brief delete a flash value
     * @param string $storeKey stored key
     * @return $this
     */
    public function deleteInFlash(string $storeKey):static{
        unset($_SESSION[SessionStorageMapping::FLASH_DATA->value][$storeKey]);

        return $this;
    }

    /**
     * @brief manage the lifecycle of flash data's
     * @return $this
     */
    public function manageFlashData():static{
        if(!isset($_SESSION[SessionStorageMapping::FLASH_DATA->value]) )
            $_SESSION[SessionStorageMapping::FLASH_DATA->value] = [];

        foreach($_SESSION[SessionStorageMapping::FLASH_DATA->value] as $key => $flashConfig){
            # check the duration and expiration
            if(
                $flashConfig["config"]["countOfRedirectBefore"] === 0 ||
                time() - $flashConfig["config"]["storeTime"] >= $flashConfig["config"]["timeBeforeDelete"]
            ){
                unset($_SESSION[SessionStorageMapping::FLASH_DATA->value][$key]);
                continue;
            }

            $flashConfig["config"]["countOfRedirectBefore"]--;

            $_SESSION[SessionStorageMapping::FLASH_DATA->value][$key] = $flashConfig;
        }

        return $this;
    }
}