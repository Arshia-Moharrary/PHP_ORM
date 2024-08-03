<?php

namespace App\Database;

use App\Exceptions\InsertFailedException;

class PDOQueryBuilder {
    private $table;
    private $connection;

    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    public function table(string $table) {
        $this->table = $table;
        return $this;
    }

    public function insert(array $data) {
        $columns = implode(",", array_keys($data));
        
        // Generate placeholders string
        $placeholders = [];

        foreach ($data as $value) {
            $placeholders[] = "?";
        }

        $placeholders = implode(",", $placeholders);

        try {
            $pdo = $this->connection;
            $sql = "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$placeholders})";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $id = $pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw new InsertFailedException();
        }

        return (int) $id;
    }
}