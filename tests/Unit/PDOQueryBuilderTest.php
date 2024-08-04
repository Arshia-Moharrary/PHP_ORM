<?php

include "vendor/autoload.php";

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use PHPUnit\Framework\TestCase;
use App\Helpers\Config;
use App\Exceptions\InsertFailedException;
use App\Exceptions\UpdateFailedException;

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
        $this->initData();

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
        $this->assertEquals(1, $result);
    }

    // ---- update method ----
    
    public function testItCanUpdateData() {
        $this->initData();

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $result = $qb->table("users")
        ->where("username", "=", "arshia.moharrary")
        ->where("email", "=", "arshia.moharrary@gmail.com")
        ->update(["email" =>     "arshia@gmail.com"]);

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testUpdateMethodReturnExceptionIfIUpdateOperationFailed() {
        $this->expectException(UpdateFailedException::class);
        $this->initData();

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $qb->table("baby")
        ->where("username", "=", "arshia.moharrary")
        ->where("email", "=", "arshia.moharrary@gmail.com")
        ->update(["email" =>     "arshia@gmail.com"]); // baby table is not exist
    }

    // ---- other ----

    public function tearDown() :void {
        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $qb->reset();

        parent::tearDown();
    }

    public function initData() {
        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $data = [
            "username" => "arshia.moharrary",
            "password" => "arshiaarshia",
            "email" => "arshia.moharrary@gmail.com",
        ];

        $qb->table("users")->insert($data);
    }
}