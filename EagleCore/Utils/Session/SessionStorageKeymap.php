<?php

namespace Yahvya\EagleFramework\Utils\Session;

/**
 * @brief Framework internal session keys
 */
enum SessionStorageKeymap: string
{
    /**
     * @brief Key which stores the framework user values
     */
    case FOR_USER = "FOR_USER";

    /**
     * @brief Key which stores the framework flash values
     */
    case FOR_FLASH = "FOR_FLASH";

    /**
     * @brief Key which stores the framework values
     */
    case FOR_FRAMEWORK = "FOR_FRAMEWORK";

    /**
     * @brief Key which stores the csrf data
     */
    case FOR_CSRF_TOKEN = "FOR_CSRF_TOKEN";
}
