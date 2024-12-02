<?php

namespace app\models;

class User extends Model {

    protected $table = 'users';  // The table name for this model

    /**
     * Fetch all users.
     */
    public function getAllUsers() {
        // Retrieve all users from the database
        return $this->findAll();
    }

    /**
     * Find a user by email.
     * Returns the first matching user.
     * 
     * @param string $email
     * @return object|null
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM $this->table WHERE email = :email LIMIT 1";
        $results = $this->query($query, ['email' => $email]);
        return $results ? (object)$results[0] : null; // Ensure it returns an object
    }
    
    /**
     * Create a new user with just an email.
     * 
     * @param array $data
     * @return int|null The user ID or null if not successful
     */
    public function saveUser($data) {
        // Prepare an insert query to add a new user
        $query = "INSERT INTO $this->table (email) VALUES (:email)";
        
        // Execute the insert query
        $this->query($query, ['email' => $data['email']]);
        
        // Return the last inserted ID (the user ID)
        return $this->lastInsertId();
    }

    /**
     * Delete a user by ID.
     * 
     * @param int $userId
     * @return bool Whether the deletion was successful
     */
    public function deleteUser($userId) {
        // Delete a user based on their ID
        return $this->deleteBy('id', $userId);
    }

    /**
     * Find a user by their ID.
     * 
     * @param int $id
     * @return object|null
     */
    public function findById($id) {
        $query = "SELECT * FROM $this->table WHERE id = :id LIMIT 1";
        $results = $this->query($query, ['id' => $id]);
        return $results ? $results[0] : null;
    }
}
