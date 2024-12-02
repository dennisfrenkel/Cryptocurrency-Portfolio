<?php

namespace app\controllers;

use app\models\User;

class UserController extends Controller {

    /**
     * Fetch all users (returns JSON).
     */
    public function getUsers() {
        $userModel = new User();
        $users = $userModel->getAllUsers();
        $this->returnJSON($users ?: ['status' => 'error', 'message' => 'No users found']);
    }

    /**
     * Displays the login page (HTML page).
     */
    public function loginView() {
        $this->returnView('login/login.html', true);
    }

    /**
     * Login or register a user based on email.
     */
    public function login() {
        $this->ensureSessionStarted();
    
        try {
            $data = json_decode(file_get_contents("php://input"), true);
    
            // Check if email is provided and is valid
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->returnJSON(['status' => 'error', 'message' => 'A valid email is required']);
                return;
            }
    
            // Get user by email from the database
            $userModel = new User();
            $user = $userModel->findByEmail($data['email']);
    
            if (!$user) {
                // If user doesn't exist, create a new user
                $newUserId = $userModel->saveUser(['email' => $data['email']]);
                if ($newUserId) {
                    $_SESSION['user_id'] = $newUserId;
                    $_SESSION['email'] = $data['email'];
                    $this->returnJSON(['status' => 'success', 'message' => 'User registered and logged in']);
                } else {
                    $this->returnJSON(['status' => 'error', 'message' => 'Failed to create user']);
                }
            } else {
                // If user exists, log them in
                $_SESSION['user_id'] = $user->id;   // <-- Use object notation instead of array
                $_SESSION['email'] = $user->email;  // <-- Use object notation instead of array
                $this->returnJSON(['status' => 'success', 'message' => 'Login successful']);
            }
        } catch (\Exception $e) {
            // Log error if there's an exception
            error_log('Login Exception: ' . $e->getMessage());
            $this->returnJSON(['status' => 'error', 'message' => 'An unexpected error occurred.']);
        }
    }
    
    /**
     * Logout a user and destroy the session.
     */
    public function logout() {
        $this->ensureSessionStarted();

        // Destroy the session and logout the user
        session_destroy();
        $this->returnJSON(['status' => 'success', 'message' => 'Logged out successfully']);
    }

    /**
     * Check if a user is authenticated.
     */
    public function isAuthenticated() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->returnJSON(['status' => 'error', 'message' => 'User not authenticated']);
            exit();
        }

        $this->returnJSON(['status' => 'success', 'message' => 'User is authenticated']);
    }

    /**
     * Start a session if not already started.
     */
    private function ensureSessionStarted() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
