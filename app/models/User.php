<?php

namespace app\models;

class User extends Model {

    protected $table = 'users';

    /**
     * Fetch all users.
     */
    public function getAllUsers() {
        return $this->findAll();
    }

    /**
     * Find a user by email.
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM $this->table WHERE email = :email LIMIT 1";
        $results = $this->query($query, ['email' => $email]);
        return $results ? $results[0] : null; // Ensure a single object is returned
    }

    /**
     * Create a new user with just an email.
     */
    public function saveUser($data) {
        $query = "INSERT INTO $this->table (email) VALUES (:email)";
        $this->query($query, ['email' => $data['email']]);
        return $this->lastInsertId();
    }

    /**
     * Delete a user by ID.
     */
    public function deleteUser($userId) {
        return $this->deleteBy('id', $userId);
    }
}
