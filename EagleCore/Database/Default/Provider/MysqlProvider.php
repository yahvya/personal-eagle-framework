<?php

namespace Yahvya\EagleFramework\Database\Default\Provider;

use Override;
use PDO;
use Yahvya\EagleFramework\Config\Config;
use Yahvya\EagleFramework\Config\ConfigException;
use Yahvya\EagleFramework\Database\Providers\DatabaseProvider;
use Throwable;

/**
 * @brief MYSQL connection provider
 */
class MysqlProvider extends DatabaseProvider
{
    /**
     * @var PDO|null Database shared connection instance
     */
    protected static ?PDO $con;

    #[Override]
    public function initDatabase(Config $providerConfig): void
    {
        $providerConfig->checkConfigs("host", "user", "password", "dbname");

        try
        {
            self::$con = new PDO(
                dsn: "mysql:host={$providerConfig->getConfig(name: "host")};dbname={$providerConfig->getConfig(name: "dbname")}",
                username: $providerConfig->getConfig(name: "user"),
                password: $providerConfig->getConfig(name: "password"),
                options: [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
        catch (Throwable)
        {
            throw new ConfigException(message: "Fail to connect to the database");
        }
    }

    /**
     * @return PDO|null The created PDO connection during the initialization phase or null
     */
    #[Override]
    public function getCon(): ?PDO
    {
        return self::$con;
    }
}