<?php

namespace Tests\Functional;

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use PHPUnit\Framework\TestCase;
use App\Helpers\Config;
use App\Helpers\HttpClient;

class CrudTest extends TestCase {
    private $qb; // Query builder
    private $httpClient;

    public function setUp() :void {
        $config = Config::get("database");
        $pdo = new PDODatabaseConnection($config);
        $this->qb = new PDOQueryBuilder($pdo->connect()->getConnection());
        $this->httpClient = new HttpClient();
    }

    public function tearDown() :void {
        $this->httpClient = null;
        parent::tearDown();
    }
}