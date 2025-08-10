<?php

namespace Yahvya\EagleFramework\Config;

/**
 * @brief Framework configuration
 */
enum FrameworkConfig: string
{
    /**
     * @brief Public directory path
     * @type string
     */
    case PUBLIC_DIR_PATH = "publicDirPath";

    /**
     * @brief Storage directory path
     * @type string
     */
    case STORAGE_DIR_PATH = "storageDirPath";

    /**
     * @brief Authorized file extensions to be directly accessed without being in the public directory
     * @type string[]
     */
    case AUTHORIZED_EXTENSIONS_AS_PUBLIC = "authorizedExtensionsAsPublic";

    /**
     * @brief Route registration file path
     * @type string
     */
    case ROUTES_BASEDIR_PATH = "routesBasedirPath";

    /**
     * @brief Regular expression to match generic param markers in route links
     * @type string
     * @attention The expression should capture the variable name only ex: articleName => :([a-zA-Z]+)
     */
    case ROUTE_GENERIC_PARAMETER_MATCHER = "routeGenericMatcher";
}