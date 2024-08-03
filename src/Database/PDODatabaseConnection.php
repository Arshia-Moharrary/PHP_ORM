<?php

namespace App\Database;
use App\Contracts\DatabaseConnectionInterface;
use App\Exceptions\PDODatabaseConnectionException;
use App\Exceptions\InvalidConfigFileException;
use App\Helpers\Config;


class PDODatabaseConnection implements DatabaseConnectionInterface {
    private $config;
    private $connection;

    public function __construct(object $config) {
        $validation = Config::validate($config, "databaseConfig");

        if (!$validation) {
            throw new InvalidConfigFileException("The config file is invalid (your config must have these keys: host, database, db_user, db_password, rdbms)");
        }
        $this->config = $config;
    }

    public function connect() {
        $dsn = $this->generateDsn();

        try {
            $pdo = new \PDO(...$dsn);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            throw new PDODatabaseConnectionException($e->getMessage());
        }

        $this->connection = $pdo;

        return $this;
    }

    public function getConnection() {
        return $this->connection;
    }

    private function generateDsn() {
        $config = $this->config;

        $dsn = "{$config->rdbms}:host={$config->host};dbname={$config->database}";
        return [
            $dsn,
            $config->db_user,
            $config->db_password
        ];
    }
}