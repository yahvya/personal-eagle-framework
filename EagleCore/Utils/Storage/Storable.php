<?php

namespace Yahvya\EagleFramework\Utils\Storage;

/**
 * @brief Represent an element which can be stored
 */
interface Storable
{
    /**
     * @brief Stock the element
     * @param string $path Expected storage path
     * @return bool If the store action succeeds
     */
    public function storeIn(string $path): bool;

    /**
     * @return mixed The stored element content
     */
    public function getFromStorage(): mixed;
}