<?php

use SaboCore\Configuration\ApplicationConfiguration;
use SaboCore\Configuration\EnvConfig;

# --------------------------------------------------------------------
# framework default global functions
# --------------------------------------------------------------------

/**
 * @param string $key database configuration env key
 * @return mixed the value or potentially null on key not found
 * @attention on production key not found warning is disabled
 */
function dbEnv(string $key):mixed{
    if(@$_ENV[EnvConfig::APPLICATION_CONFIGURATION][ApplicationConfiguration::APPLICATION_DEV_MODE])
        return $_ENV[EnvConfig::DATABASE_CONFIGURATION][$key];
    else
        return @$_ENV[EnvConfig::DATABASE_CONFIGURATION][$key];
}

/**
 * @param string $key maintenance configuration env key
 * @return mixed the value or potentially null on key not found
 * @attention on production key not found warning is disabled
 */
function maintenanceEnv(string $key):mixed{
    if(@$_ENV[EnvConfig::APPLICATION_CONFIGURATION][ApplicationConfiguration::APPLICATION_DEV_MODE])
        return $_ENV[EnvConfig::MAINTENANCE_CONFIGURATION][$key];
    else
        return @$_ENV[EnvConfig::MAINTENANCE_CONFIGURATION][$key];
}

/**
 * @param string $key mailer configuration env key
 * @return mixed the value or potentially null on key not found
 * @attention on production key not found warning is disabled
 */
function mailerEnv(string $key):mixed{
    if(@$_ENV[EnvConfig::APPLICATION_CONFIGURATION][ApplicationConfiguration::APPLICATION_DEV_MODE])
        return $_ENV[EnvConfig::MAILER_CONFIGURATION][$key];
    else
        return @$_ENV[EnvConfig::MAILER_CONFIGURATION][$key];
}

/**
 * @param string $key application configuration env key
 * @return mixed the value or potentially null on key not found
 * @attention on production key not found warning is disabled
 */
function appEnv(string $key):mixed{
    if(@$_ENV[EnvConfig::APPLICATION_CONFIGURATION][ApplicationConfiguration::APPLICATION_DEV_MODE])
        return $_ENV[EnvConfig::APPLICATION_CONFIGURATION][$key];
    else
        return @$_ENV[EnvConfig::APPLICATION_CONFIGURATION][$key];
}

/**
 * @param string $key custom configuration env key
 * @return mixed the value or potentially null on key not found
 * @attention on production key not found warning is disabled
 */
function customEnv(string $key):mixed{
    if(@$_ENV[EnvConfig::APPLICATION_CONFIGURATION][ApplicationConfiguration::APPLICATION_DEV_MODE])
        return $_ENV[EnvConfig::CUSTOM_CONFIGURATION][$key];
    else
        return @$_ENV[EnvConfig::CUSTOM_CONFIGURATION][$key];
}

# --------------------------------------------------------------------
# define your functions
# --------------------------------------------------------------------