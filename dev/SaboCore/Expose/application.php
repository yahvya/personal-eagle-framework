<?php

// APPLICATION UTILS

use SaboCore\Core\Global\ApplicationConfiguration;
use SaboCore\Core\Global\FrameworkConfiguration;

/**
 * @return ApplicationConfiguration A singleton application configuration
 */
function application(): ApplicationConfiguration
{
    static $applicationConfiguration = new ApplicationConfiguration();

    return $applicationConfiguration;
}

/**
 * @return FrameworkConfiguration A singleton framework configuration
 */
function framework():FrameworkConfiguration
{
    static $frameworkConfiguration = new FrameworkConfiguration();

    return $frameworkConfiguration;
}