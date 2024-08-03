<?php

include "vendor/autoload.php";

use App\Helpers\Config;
use PHPUnit\Metadata\Uses;

$config = Config::get("database");
unset($config->database);

var_dump($config);