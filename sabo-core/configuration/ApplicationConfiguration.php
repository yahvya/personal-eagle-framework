<?php

namespace SaboCore\Configuration;

/**
 * @brief application configuration keys
 */
readonly abstract class ApplicationConfiguration{
    /**
     * @const your application name
     * @required
     */
    public const string APPLICATION_NAME = "APPLICATION_NAME";

    /**
     * @const your application link
     * @required
     */
    public const string APPLICATION_LINK = "APPLICATION_LINK";

    /**
     * @const version of your application
     */
    public const string APPLICATION_VERSION = "APPLICATION_VERSION";

    /**
     * @const development mode, represented by a boolean. True for dev - False for prod
     * @required
     */
    public const string APPLICATION_DEV_MODE = "APPLICATION_DEV_MODE";

    /**
     * @const assets version. used to reload your assets on changing by bypassing navigators cache
     * @required
     */
    public const string APPLICATION_ASSETS_VERSION = "APPLICATION_ASSETS_VERSION";
}