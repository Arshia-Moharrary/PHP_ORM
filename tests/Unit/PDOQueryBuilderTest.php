<?php

include "vendor/autoload.php";

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use PHPUnit\Framework\TestCase;
use App\Helpers\Config;
use App\Exceptions\InsertFailedException;

class PDOQueryBuilderTest extends TestCase {
    // ---- insert method tests ----

    public function testItCanInsertData() {
        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $data = [
            "username" => "arshia.moharrary",
            "password" => "arshiaarshia",
            "email" => "arshia.moharrary@gmail.com",
        ];

        $result = $qb->table("users")->insert($data);

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testInsertMethodReturnExceptionIfInsertOperationFailed() {
        $this->expectException(InsertFailedException::class);

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $data = [
            "username" => "arshia.moharrary",
            "password" => "arshiaarshia",
            "email" => "arshia.moharrary@gmail.com",
        ];

        $result = $qb->table("baby")->insert($data); // baby table not exist

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }
}