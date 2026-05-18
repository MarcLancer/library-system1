<?php

namespace LibrarySystem\Core;

use PDO;
use PDOException;

class Database {
    private PDO $connection;
    private $config;

    public function __construct($config) {
        $this->config = $config;
        $this->connect();
    }

    private function connect(): void {
        try {
            $config = $this->config['connections']['mysql'];
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function query($sql, $params = []): ?array {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception('Query failed: ' . $e->getMessage());
        }
    }

    public function queryOne($sql, $params = []): ?array {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            throw new \Exception('Query failed: ' . $e->getMessage());
        }
    }

    public function insert($table, $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array_values($data));
            return (int)$this->connection->lastInsertId();
        } catch (PDOException $e) {
            throw new \Exception('Insert failed: ' . $e->getMessage());
        }
    }

    public function update($table, $data, $where, $params = []): int {
        $set = implode(', ', array_map(fn($key) => "{$key} = ?", array_keys($data)));
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        $allParams = array_merge(array_values($data), $params);

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($allParams);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new \Exception('Update failed: ' . $e->getMessage());
        }
    }

    public function delete($table, $where, $params = []): int {
        $sql = "DELETE FROM {$table} WHERE {$where}";

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new \Exception('Delete failed: ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}
