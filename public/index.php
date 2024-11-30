<?php
require '../app/core/setup.php';

use app\core\Router;

try {
    // Instantiate the Router to handle incoming requests
    // Troubleshooting 
    $router = new Router();
} catch (Exception $e) {
    if (DEBUG) {
        echo "<h1>Application Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
    } else {
        error_log($e->getMessage(), 3, '../logs/errors.log');
        echo "<h1>An error occurred. Please try again later.</h1>";
    }
}
?>
