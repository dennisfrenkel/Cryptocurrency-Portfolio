<?php

namespace app\controllers;

abstract class Controller {

    public function returnView($pathToView, $usePublicPath = false) {
        $basePath = $usePublicPath ? PUBLIC_VIEW_PATH : VIEW_PATH;
        $fullPath = $basePath . '/' . ltrim($pathToView, '/');  
    
        if (!file_exists($fullPath)) {
            $backtrace = debug_backtrace();
            $caller = $backtrace[1];

            $errorMessage = "Error: View not found - {$fullPath}\n";
            $errorMessage .= "Called in: {$caller['file']} on line {$caller['line']}\n";
            $errorMessage .= "Calling function: {$caller['function']}";

            die(nl2br($errorMessage));
        }
    
        require $fullPath;
        exit();
    }

    public function returnJSON($json, $statusCode = 200) {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        echo json_encode($json);
        exit();
    }

    public function isAuthenticated() {
        if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
            return (int) $_SESSION['user_id'];
        }

        $this->returnJSON(['status' => 'error', 'message' => 'Unauthorized'], 401);
    }

    protected function ensureSessionStarted() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
