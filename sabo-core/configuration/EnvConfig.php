<?php

namespace SaboCore\Configuration;

/**
 * @brief env configuration keys
 */
readonly abstract class EnvConfig{
    /**
     * @const application configuration
     * @required
     */
    public const string APPLICATION_CONFIGURATION = "APPLICATION_CONFIGURATION";

    /**
     * @const database configuration
     * @required
     */
    public const string DATABASE_CONFIGURATION = "DATABASE_CONFIGURATION";

    /**
     * @const maintenance configuration
     * @required
     */
    public const string MAINTENANCE_CONFIGURATION = "MAINTENANCE_CONFIGURATION";

    /**
     * @const mailer configuration
     * @required
     */
    public const string MAILER_CONFIGURATION = "MAILER_CONFIGURATION";

    /**
     * @const custom configuration
     * @required 
     */
    public const string CUSTOM_CONFIGURATION = "CUSTOM_CONFIGURATION";
}