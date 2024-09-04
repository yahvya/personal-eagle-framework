<?php

namespace SaboCore\PathConfig;

/**
 * @brief application path map
 */
enum AppPathMap:string{
    /**
     * @brief configuration files parent directory
     */
    case CONFIGURATIONS_DIRECTORY = "/configs";

    /**
     * @brief routes configurations parent directory
     */
    case ROUTES_DIRECTORY = "/routes";

    /**
     * @brief public directory path
     * @attention this directory path is linked with the htaccess file and the entrypoint
     */
    case PUBLIC_DIRECTORY = "/public";

    /**
     * @brief user sources directory path
     */
    case SOURCES_DIRECTORY = "/src";

    /**
     * @brief private storage directory path
     */
    case STORAGE_DIRECTORY = "/storage";

    /**
     * @brief tests directory path
     */
    case TESTS_DIRECTORY = "/tests";
}