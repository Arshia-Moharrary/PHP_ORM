<?php

namespace App\Database;

use App\Exceptions\InsertFailedException;
use App\Exceptions\UpdateFailedException;
use App\Exceptions\SelectFailedException;
use PDOException;

class PDOQueryBuilder {
    private $table;
    private $connection;
    private $conditions = [];
    private $values = [];

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

    public function where(string $column, string $operator, string $value) {
        $this->conditions[] = "{$column} {$operator} ?";
        $this->values[] = $value;
        return $this;
    }

    public function update(array $data) {
        foreach ($data as $key => $value) {
            $update = "{$key} = '{$value}'";
        }

        $conditions = implode(" and ", $this->conditions);

        if (!empty($conditions)) {
            $conditions = "WHERE " . $conditions;
        }

        try {
            $pdo = $this->connection;
            $sql = "UPDATE {$this->table} SET {$update} {$conditions}";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($this->values);
            $rows = $stmt->rowCount();
        } catch (PDOException $e) {
            throw new UpdateFailedException();
        }

        return $rows;
    }

    public function select(array $column = []) {
        $select = "*";

        if (count($column)) {
            $select = implode(",", $column);
        }

        $conditions = implode(" and ", $this->conditions);

        if (!empty($conditions)) {
            $conditions = "WHERE " . $conditions;
        }

        try {
            $pdo = $this->connection;
            $sql = "SELECT {$select} FROM {$this->table} {$conditions}";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($this->values));
            $fetch = $stmt->fetchAll(\PDO::FETCH_OBJ);

            return $fetch;
        } catch (PDOException $e) {
            echo $e->getMessage();
            echo $sql;
            throw new SelectFailedException();
        }
    }

    public function reset() {
        // ! Note: this method delete all table records !
        $pdo = $this->connection;
        $sql = "SHOW TABLES";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($result as $table) {
            $sql = "TRUNCATE TABLE {$table}";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
    }
}