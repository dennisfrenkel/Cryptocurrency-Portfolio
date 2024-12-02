<?php
namespace app\core;

use app\controllers\MainController;
use app\controllers\UserController;
use app\controllers\TransactionController;

class Router {
    public $urlArray;

    public function __construct() {
        // Start the session if not started 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->urlArray = $this->routeSplit();

        // Redirect to login if user is not authenticated and accessing a protected route
        if (!$this->isAuthenticated() && !$this->isLoginRoute()) {
            // Store the current page in session to redirect after login
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            error_log("Redirecting unauthenticated user to /login");
            header('Location: /login');
            exit();
        }

        // Handle the routes for main, user, and transaction
        $this->handleMainRoutes();
        $this->handleUserRoutes();
        $this->handleTransactionRoutes();
    }

    /**
     * handles query parameters
     */
    protected function routeSplit() {
        // Split the URL path and query string
        $removeQueryParams = strtok($_SERVER["REQUEST_URI"], '?');
        return explode("/", trim($removeQueryParams, '/'));
    }

    /**
     * Check if the user is authenticated
     */
    protected function isAuthenticated() {
        // Ensure that the user is authenticated by checking the session variable
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Check if the current route is the login route
     */
    protected function isLoginRoute() {
        return $this->urlArray[0] === 'login' || ($this->urlArray[0] === 'api' && $this->urlArray[1] === 'login');
    }

    /**
     * protected routes
     */
    protected function isProtectedRoute() {
        $protectedRoutes = ['transactions', 'api/transactions', 'api/chatgpt', 'api/crypto-prices'];
        return in_array($this->urlArray[0], $protectedRoutes) || in_array($this->urlArray[1], $protectedRoutes);
    }

    /**
     * Main page routes
     */
    protected function handleMainRoutes() {
        $mainController = new MainController();

        // Handle homepage route
        if (empty($this->urlArray[0]) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            error_log("Rendering homepage...");
            $mainController->homepage();
            return;
        }

        // Handle API routes for chatgpt and crypto prices
        if ($this->urlArray[0] === 'api' && $this->urlArray[1] === 'chatgpt' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Processing ChatGPT API request...");
            $mainController->getChatGPTRecommendation();
            return;
        }

        // Handle crypto prices API route
        if ($this->urlArray[0] === 'api' && $this->urlArray[1] === 'crypto-prices' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            error_log("Fetching live crypto prices...");
            $mainController->getCryptoPrices();
            return;
        }
    }

    /**
     * Handle user-related routes
     */
    protected function handleUserRoutes() {
        $userController = new UserController();

        // Handle login route
        if ($this->urlArray[0] === 'login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            error_log("Rendering login page...");
            $userController->loginView(); // Render the login page
            return;
        }

        // Handle login API route (POST request)
        if ($this->urlArray[0] === 'api' && $this->urlArray[1] === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Processing login request...");
            $userController->login(); 
            return;
        }

        // logout API route (POST request)
        if ($this->urlArray[0] === 'api' && $this->urlArray[1] === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Processing logout request...");
            $userController->logout();
            return;
        }

        // get all users (Admin route)
        if ($this->urlArray[0] === 'api' && $this->urlArray[1] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            error_log("Fetching all users...");
            $userController->getUsers();
            return;
        }
    }

    /**
     * Transaction-related routes
     */
    protected function handleTransactionRoutes() {
        $transactionController = new TransactionController();

        // transactions view
        if ($this->urlArray[0] === 'transactions' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            error_log("Rendering transactions view...");
            $transactionController->transactionsView(); 
            return;
        }

        // transaction-related API routes (GET, POST, DELETE)
        if ($this->urlArray[0] === 'api' && $this->urlArray[1] === 'transactions') {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                error_log("Fetching transactions...");
                $transactionController->getTransactions();
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                error_log("Saving transaction...");
                $transactionController->saveTransaction(); 
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                error_log("Deleting transaction...");
                $transactionController->deleteTransaction(); 
                return;
            }
        }

        // transaction summary route
        if ($this->urlArray[0] === 'api' && $this->urlArray[1] === 'transactions' && $this->urlArray[2] === 'summary' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            error_log("Fetching transaction summary...");
            $transactionController->getTransactionSummary();
            return;
        }
    }
}
