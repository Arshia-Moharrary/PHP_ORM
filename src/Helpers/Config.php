<?php

namespace App\Helpers;

use App\Exceptions\InvalidConfigFileException;

class Config {
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
}