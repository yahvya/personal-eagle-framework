<?php

namespace SaboCore\Routing\Request;

/**
 * @brief session storage keys map
 */
enum SessionStorageMapping:string{
    /**
     * @brief storage reserved for framework
     */
    case FRAMEWORK_STORAGE = "FRAMEWORK_STORAGE";

    /**
     * @brief storage reserved for user values
     */
    case USER_STORAGE = "USER_STORAGE";

    /**
     * @brief storage reserved for flash data's
     */
    case FLASH_DATA = "FLASH_DATA";
}