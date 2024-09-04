<?php

namespace SaboCore\Database\Providers\Connection;

use PDO;
use PDOException;

/**
 * @brief mysql connection provider
 */
readonly class MysqlConnection implements ConnectionProvider {
    /**
     * @var PDO|null database con
     */
    protected ?PDO $con;

    /**
     * @const framework pdo default configuration
     */
    public const array PDO_DEFAULT_OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    ];

    /**
     * @param string $host database host
     * @param string $user database user
     * @param string $password database access password
     * @param string $database database name
     * @param string|null $port database port, if not specified the default one will be used by pdo
     * @param array $pdoCustomOptions pdo custom options, this array will totally replace the default options to allow deletions.
     */
    public function __construct(
        public string $host,
        public string $user,
        public string $password,
        public string $database,
        public ?string $port = null,
        public array $pdoCustomOptions = self::PDO_DEFAULT_OPTIONS
    ){
        $this->con = NULL;
    }

    /**
     * @brief provide a database connection
     * @param bool $forceNew
     * @return PDO database connection
     * @throws ConnectionProviderException in case of a PDOException
     */
    public function getConnection(bool $forceNew = false): PDO {
        try{
            if($this->con !== NULL && !$forceNew)
                return $this->con;

            $portDefinition = $this->port === null ? "" : "port=$this->port;";

            return new PDO(
                dsn: "mysql:host=$this->host;dbname=$this->database;$portDefinition",
                username: $this->user,
                password: $this->password,
                options: $this->pdoCustomOptions
            );
        }
        catch(PDOException $e){
            throw new ConnectionProviderException(errorMessage: $e->getMessage(),baseException: $e);
        }
    }
}