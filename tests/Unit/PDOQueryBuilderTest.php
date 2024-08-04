<?php

include "vendor/autoload.php";

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use PHPUnit\Framework\TestCase;
use App\Helpers\Config;
use App\Exceptions\InsertFailedException;
use App\Exceptions\UpdateFailedException;
use App\Exceptions\WhereNotFoundException;
use App\Exceptions\TableNotFoundException;

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

    public function testInsertMethodThrowExceptionIfTableNotSet() {
        $this->expectException(TableNotFoundException::class);
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

        $qb->insert($data);
    }

    // ---- where method tests ----

    public function testItCanHandleMultipleWhere() {
        $this->initData();
        $this->initData(["username" => "reza.ahmadi"]);

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $result = $qb->table("users")
        ->where("username", "=", "arshia.moharrary")
        ->where("password", "=", "arshiaarshia")
        ->update(["password" => "1234"]);

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
        ->update(["email" => "arshia@gmail.com"]);

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
        ->update(["email" => "arshia@gmail.com"]); // baby table is not exist
    }

    public function testUpdateMethodReturnZeroIfRecordNotExist() {
        $this->initData();

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $result = $qb->table("users")
        ->where("username", "=", "baby") // baby username not exist
        ->update(["email" => "test@gmail.com"]);

        $this->assertEquals(0, $result);
    }

    public function testUpdateMethodThrowExceptionIfWhereNotSet() {
        $this->initData();
        $this->expectException(WhereNotFoundException::class);

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $qb->table("users")
        ->update(["email" => "test@gmail.com"]);
    }

    public function testUpdateMethodThrowExceptionIfTableNotSet() {
        $this->initData();
        $this->expectException(TableNotFoundException::class);

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $qb->where("username", "=", "arshia.moharrary")->update(["email" => "test@gmail.com"]);
    }

    // ---- select method tests ----

    public function testItCanSelectData() {
        $this->initData();

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $result = $qb->table("users")
        ->where("username", "=", "arshia.moharrary")
        ->select(["email"]);

        $email = $result[0]->email;

        $this->assertEquals("arshia.moharrary@gmail.com", $email);
    }

    public function testSelectMethodReturnEmptyArrayIfRecordNotExist() {
        $this->initData();
        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());
        
        $result = $qb->table("users")
        ->where("username", "=", "baby") // baby username is not exist
        ->select();

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    public function testSelectMethodThrowExceptionIfTableNotSet() {
        $this->expectException(TableNotFoundException::class);
        $this->initData();

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());
        
        $qb->where("username", "=", "baby")->select();
    }

    // ---- delete method tests ----

    public function testItCanDeleteData() {
        $this->initData();

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());
        
        $result = $qb->table("users")
        ->where("username", "=", "arshia.moharrary")
        ->delete();

        $this->assertIsInt($result);
        $this->assertEquals(1, $result);
    }

    public function testDeleteMethodReturnZeroIfRecordNotExist() {
        $this->initData();

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());
        
        $result = $qb->table("users")
        ->where("username", "=", "baby") // baby username not exist
        ->delete();

        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
    }

    public function testDeleteMethodThrowExceptionIfWhereNotSet() {
        $this->initData();
        $this->expectException(WhereNotFoundException::class);

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());
        
        $qb->table("users")->delete();
    }

    public function testDeleteMethodThrowExceptionIfTableNotSet() {
        $this->initData();
        $this->expectException(TableNotFoundException::class);

        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());
        
        $qb->where("username", "=", "arshia.moharrary")->delete();
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

    public function initData(array $options = []) {
        $config = Config::get("database");
        $config->database = "orm_test";
        $pdo = new PDODatabaseConnection($config);
        $qb = new PDOQueryBuilder($pdo->connect()->getConnection());

        $data = array_merge([
            "username" => "arshia.moharrary",
            "password" => "arshiaarshia",
            "email" => "arshia.moharrary@gmail.com",
        ], $options);

        $qb->table("users")->insert($data);
    }
}