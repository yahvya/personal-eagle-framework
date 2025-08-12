<?php

namespace Yahvya\EagleFramework\Utils\Session;

use Exception;
use Yahvya\EagleFramework\Utils\Csrf\CsrfManager;

/**
 * @brief Session storage handler
 */
class SessionStorage
{
    /**
     * @var int Flash data manage method call count
     */
    private static int $FLASH_DATA_MANAGE_CALL_COUNT = 0;

    /**
     * @brief Store user data
     * @param string $storeKey Store key
     * @param mixed $toStore Data to store
     * @return $this
     */
    public function store(string $storeKey, mixed $toStore): SessionStorage
    {
        $_SESSION[SessionStorageKeymap::FOR_USER->value][$storeKey] = $toStore;

        return $this;
    }

    /**
     * @brief Store a flash data (limited by time and refresh count)
     * @param string $storeKey Data key
     * @param mixed $toStore Data
     * @param int $countOfRedirectBefore Count of refresh before the suppression of the flash data
     * @param int $timeBeforeDelete Time in seconds before the deletion of the data
     * @return $this
     */
    public function storeFlash(string $storeKey, mixed $toStore, int $countOfRedirectBefore = 1, int $timeBeforeDelete = 1800): SessionStorage
    {
        $_SESSION[SessionStorageKeymap::FOR_FLASH->value][$storeKey] = [
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
     * @brief Store framework data
     * @param string $storeKey Data key
     * @param mixed $toStore Data
     * @return $this
     */
    public function storeFramework(string $storeKey, mixed $toStore): SessionStorage
    {
        $_SESSION[SessionStorageKeymap::FOR_FRAMEWORK->value][$storeKey] = $toStore;

        return $this;
    }

    /**
     * @param string $storeKey Data key
     * @return mixed User stored data associated with the key or null if not found
     */
    public function getValue(string $storeKey): mixed
    {
        return $_SESSION[SessionStorageKeymap::FOR_USER->value][$storeKey] ?? null;
    }

    /**
     * @param string $storeKey Data key
     * @return mixed Framework stored data associated with the key or null if not found
     */
    public function getFrameworkValue(string $storeKey): mixed
    {
        return $_SESSION[SessionStorageKeymap::FOR_FRAMEWORK->value][$storeKey] ?? null;
    }

    /**
     * @param string $storeKey Data key
     * @return mixed Flash stored data associated with the key or null if not found
     */
    public function getFlashValue(string $storeKey): mixed
    {
        return isset($_SESSION[SessionStorageKeymap::FOR_FLASH->value][$storeKey]) ?
            $_SESSION[SessionStorageKeymap::FOR_FLASH->value][$storeKey]["value"] :
            null;
    }

    /**
     * @brief Delete a user session data
     * @param string $storeKey Data key
     * @return $this
     */
    public function delete(string $storeKey): SessionStorage
    {
        unset($_SESSION[SessionStorageKeymap::FOR_USER->value][$storeKey]);

        return $this;
    }

    /**
     * @brief Delete a framework session data
     * @param string $storeKey Data key
     * @return $this
     */
    public function deleteInFramework(string $storeKey): SessionStorage
    {
        unset($_SESSION[SessionStorageKeymap::FOR_FRAMEWORK->value][$storeKey]);

        return $this;
    }

    /**
     * @brief Delete a flash session data
     * @param string $storeKey Data key
     * @return $this
     */
    public function deleteInFlash(string $storeKey): SessionStorage
    {
        unset($_SESSION[SessionStorageKeymap::FOR_FLASH->value][$storeKey]);

        return $this;
    }

    /**
     * @brief Handle flash data
     * @attention This method should be called, one time in the lifecycle
     * @return $this
     * @throws Exception On error
     */
    public function manageFlashDatas(): SessionStorage
    {
        self::$FLASH_DATA_MANAGE_CALL_COUNT++;

        if (self::$FLASH_DATA_MANAGE_CALL_COUNT > 1)
            throw new Exception(message: "The flash data manage method have been called more than one time");

        if (!isset($_SESSION[SessionStorageKeymap::FOR_FLASH->value])) $_SESSION[SessionStorageKeymap::FOR_FLASH->value] = [];

        foreach ($_SESSION[SessionStorageKeymap::FOR_FLASH->value] as $key => $flashConfig)
        {
            if (
                $flashConfig["config"]["countOfRedirectBefore"] === 0 ||
                time() - $flashConfig["config"]["storeTime"] >= $flashConfig["config"]["timeBeforeDelete"]
            )
            {
                unset($_SESSION[SessionStorageKeymap::FOR_FLASH->value][$key]);
                continue;
            }

            $flashConfig["config"]["countOfRedirectBefore"]--;

            $_SESSION[SessionStorageKeymap::FOR_FLASH->value][$key] = $flashConfig;
        }

        return $this;
    }

    /**
     * @brief Store a token
     * @param CsrfManager $csrfManager Csrf manager
     * @return $this
     */
    public function storeCsrf(CsrfManager $csrfManager): SessionStorage
    {
        $_SESSION[SessionStorageKeymap::FOR_CSRF_TOKEN->value][$csrfManager->token] = $csrfManager->serialize();

        return $this;
    }

    /**
     * @param string $token Csrf associated token
     * @return CsrfManager|null Associated csrf manager ou null
     */
    public function getCsrfFrom(string $token): CsrfManager|null
    {
        return isset($_SESSION[SessionStorageKeymap::FOR_CSRF_TOKEN->value][$token]) ?
            CsrfManager::deserialize(instance: $_SESSION[SessionStorageKeymap::FOR_CSRF_TOKEN->value][$token]) :
            null;
    }

    /**
     * @brief Delete a csrf token
     * @param CsrfManager $csrfManager Csrf manager
     * @return $this
     */
    public function deleteCsrf(CsrfManager $csrfManager): SessionStorage
    {
        unset($_SESSION[SessionStorageKeymap::FOR_CSRF_TOKEN->value][$csrfManager->token]);

        return $this;
    }

    /**
     * @return SessionStorage Session storage instance
     */
    public static function create(): SessionStorage
    {
        return new SessionStorage();
    }
}