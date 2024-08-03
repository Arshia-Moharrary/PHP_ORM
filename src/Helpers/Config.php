<?php

namespace App\Helpers;

use App\Exceptions\InvalidConfigFileException;

class Config {
    const REQUIRED_DB_KEYS = [
        "host",
        "database",
        "db_user",
        "db_password",
        "rdbms"
    ];

    public static function file($fileName) {
        $filePath = realpath(dirname(__DIR__) . "/config/" . $fileName . ".json");

        if (!$filePath) throw new InvalidConfigFileException(); // Throw a exception if file not found

        $fileContent = file_get_contents($filePath);
        $config = json_decode($fileContent);
        
        return $config;
    }

    public static function get($fileName, $key = null) {
        $config = self::file($fileName);

        if (is_null($key)) return $config;

        $result = $config->$key ?? null;

        return $result;
    }

    public static function validate($config, $type) {
        $config = (array) $config;

        // databaseConfig: for validation db config
        if ($type == "databaseConfig") {
            $configKeys = array_keys($config);
            return $configKeys == self::REQUIRED_DB_KEYS;
        }
    }
}