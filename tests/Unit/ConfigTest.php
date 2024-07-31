<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Exceptions\InvalidConfigFileException;
use App\Helpers\Config;

class ConfigTest extends TestCase {
    // ---- File method tests ----

    public function testFileMethodThrowExceptionIfFileNotExist() {
        $this->expectException(InvalidConfigFileException::class);
        Config::file("baby"); // Baby file not exist 
    }

    public function testFileMethodReturnContentFile() {
        $fileContent = Config::file("database");
        $exceptedData = (Object) [
            "host" => "localhost",
            "database" => "ORM",
            "db_user" => "root",
            "db_password" => "",
            "rdbms" => "mysql"
        ];

        $this->assertEquals($exceptedData, $fileContent);
    }

    public function testFileMethodReturnObject() {
        $config = Config::file("database");
        $this->assertIsObject($config);
    }

    // ---- Get method tests ----

    public function testGetMethodReturnAllContentConfigIfNotSetKey() {
        $config = Config::get("database");
        $exceptedData = (Object) [
            "host" => "localhost",
            "database" => "ORM",
            "db_user" => "root",
            "db_password" => "",
            "rdbms" => "mysql"
        ];

        $this->assertEquals($exceptedData, $config);
    }

    public function testGetMethodReturnCorrectKeyValue() {
        $config = Config::get("database", "host");
        $this->assertEquals($config, "localhost");
    }

    public function testGetMethodReturnNullIfKeyNotFound() {
        $config = Config::get("database", "baby"); // Baby key not exist
        $this->assertEquals(null, $config);
    }
}