<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\Config;
use App\Contracts\DatabaseConnectionInterface;
use App\Database\PDODatabaseConnection;
use App\Exceptions\PDODatabaseConnectionException;
use App\Exceptions\InvalidConfigFileException;

class PDODatabaseConnectionTest extends TestCase {
    // ---- Class tests ----

    public function testPDODatabaseConnectionImplementDatabaseConnectionInterface() {
        $config = Config::get("database");
        $config->database = "ORM_test";

        $pdo = new PDODatabaseConnection($config);

        $this->assertInstanceOf(DatabaseConnectionInterface::class, $pdo);
    }

    // ---- connect method tests ----

    public function testConnectMethodShoudBeConnectToDatabase() {
        $config = Config::get("database");
        $config->database = "ORM_test";

        $pdo = new PDODatabaseConnection($config);
        $pdo->connect();

        $this->assertInstanceOf(\PDO::class, $pdo->getConnection());
    }

    public function testConnectMethodThrowPDODatabaseConnectionExceptionIfCouldNotConnectToDatabase() {
        $this->expectException(PDODatabaseConnectionException::class);

        $config = Config::get("database");
        $config->database = "baby"; // baby database not exist

        $pdo = new PDODatabaseConnection($config);
        $pdo->connect();
    }

    public function testThrowExceptionIfConfigIsInvalid() {
        $this->expectException(InvalidConfigFileException::class);

        $config = Config::get("database");
        unset($config->database);

        $pdo = new PDODatabaseConnection($config);
        $pdo->connect();
    }
}