<?php

namespace app\models;

class User extends Model {

    protected $table = 'users'; // The table name for this model

    /**
     * Fetch all users.

     */
    public function getAllUsers() {
        return $this->findAll(); // Retrieve all users from the database
    }

    /**
     * Find a user by email.
     * Returns the first matching user.
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM $this->table WHERE email = :email LIMIT 1";
        $results = $this->query($query, ['email' => $email]);
        return $results ? (object)$results[0] : null; // Ensure the result is returned as an object
    }

    /**
     * Create a new user with just an email.
     */
    public function saveUser($data) {
        $query = "INSERT INTO $this->table (email, created_at, updated_at) VALUES (:email, NOW(), NOW())";
        $this->query($query, ['email' => $data['email']]);
        return $this->lastInsertId(); // Return the last inserted ID
    }

    /**
     * Delete a user by ID.
     */
    public function deleteUser($userId) {
        return $this->deleteBy('id', $userId); // Delete a user by ID
    }

    /**
     * Find a user by their ID.
     */
    public function findById($id) {
        $query = "SELECT * FROM $this->table WHERE id = :id LIMIT 1";
        $results = $this->query($query, ['id' => $id]);
        return $results ? (object)$results[0] : null; // Return the result as an object
    }

    /**
     * Update a user's email by ID.
     *
     */
    public function updateUserEmail($id, $email) {
        $query = "UPDATE $this->table SET email = :email, updated_at = NOW() WHERE id = :id";
        return $this->query($query, ['email' => $email, 'id' => $id]);
    }
}
