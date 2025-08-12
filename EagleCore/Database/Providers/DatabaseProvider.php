<?php

namespace Yahvya\EagleFramework\Database\Providers;

use Yahvya\EagleFramework\Config\Config;
use Yahvya\EagleFramework\Config\ConfigException;

/**
 * @brief Database provider
 */
abstract class DatabaseProvider
{
    /**
     * @brief Initialize the database by configure things
     * @param Config $providerConfig Provider configuration (specified in the environment configuration)
     * @return void
     * @throws ConfigException On error
     */
    public abstract function initDatabase(Config $providerConfig): void;

    /**
     * @return mixed Connection instance handler
     */
    public abstract function getCon(): mixed;
}