<?php

namespace SaboCore\Configuration;

/**
 * @brief database configuration keys
 */
readonly abstract class DatabaseConfiguration{
    /**
     * @const define if the application have to create a database con, True for yes , False for no
     * @required
     */
    public const string INIT_APP_WITH_CON = "INIT_APP_WITH_CON";

    /**
     * @const connection provider
     * @required if init app is true
     */
    public const string CONNECTION_PROVIDER = "CONNECTION_PROVIDER";
}