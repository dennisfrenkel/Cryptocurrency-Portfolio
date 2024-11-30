<?php

namespace app\models;

abstract class Model {

    protected $table; 

    public function findAll() {
        $query = "SELECT * FROM $this->table";
        return $this->query($query);
    }

    protected function connect() {
        $dsn = "mysql:host=" . DBHOST . ";dbname=" . DBNAME;
        try {
            $pdo = new \PDO($dsn, DBUSER, DBPASS);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    protected function query($query, $data = []) {
        $pdo = $this->connect();
        $stmt = $pdo->prepare($query);
        $success = $stmt->execute($data);

        if ($success) {
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        return false;
    }

    public function findBy($column, $value) {
        $query = "SELECT * FROM $this->table WHERE $column = :value";
        return $this->query($query, ['value' => $value]);
    }

    public function deleteBy($column, $value) {
        $query = "DELETE FROM $this->table WHERE $column = :value";
        return $this->query($query, ['value' => $value]);
    }
}
