<?php

namespace app\controllers;

use app\models\User;

class UserController extends Controller {

    public function getUsers() {
        $userModel = new User();
        $users = $userModel->getAllUsers();
        $this->returnJSON($users ?: ['status' => 'error', 'message' => 'No users found']);
    }

    public function loginView() {
        $this->returnView('login/login.html', true);
    }

    public function login() {
        $this->ensureSessionStarted();

        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->returnJSON(['status' => 'error', 'message' => 'A valid email is required']);
                return;
            }

            $userModel = new User();
            $user = $userModel->findByEmail($data['email']);

            if (!$user) {
                $newUserId = $userModel->saveUser(['email' => $data['email']]);
                if ($newUserId) {
                    $_SESSION['user_id'] = $newUserId;
                    $_SESSION['email'] = $data['email'];
                    $this->returnJSON(['status' => 'success', 'message' => 'User registered and logged in']);
                } else {
                    $this->returnJSON(['status' => 'error', 'message' => 'Failed to create user. Try again!']);
                }
            } else {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['email'] = $user->email;
                $this->returnJSON(['status' => 'success', 'message' => 'Login successful']);
            }
        } catch (\Exception $e) {
            error_log('Login Exception: ' . $e->getMessage());
            $this->returnJSON(['status' => 'error', 'message' => 'An unexpected error occurred.']);
        }
    }

    public function logout() {
        $this->ensureSessionStarted();
        session_destroy();
        $this->returnJSON(['status' => 'success', 'message' => 'Logged out successfully']);
    }

    public function isAuthenticated() {
        $this->ensureSessionStarted();
        if (!isset($_SESSION['user_id'])) {
            $this->returnJSON(['status' => 'error', 'message' => 'User not authenticated']);
        } else {
            $this->returnJSON(['status' => 'success', 'message' => 'User is authenticated']);
        }
    }
}
