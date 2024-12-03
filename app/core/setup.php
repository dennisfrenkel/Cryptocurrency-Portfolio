<?php

// Start session globally
session_start();

// core files
require '../app/core/Router.php';
require '../app/core/Logger.php';

// base models and controllers
require '../app/models/model.php';
require '../app/controllers/Controller.php';

// application-specific models
require '../app/models/User.php';
require '../app/models/Transaction.php';

// application-specific controllers
require '../app/controllers/MainController.php';
require '../app/controllers/UserController.php';
require '../app/controllers/TransactionController.php';

// env variables
$envPath = '../.env';

// for troubleshooting
if (!file_exists($envPath)) {
    die("Error: .env file is missing.");
}

$env = parse_ini_file($envPath);

if (!$env) {
    die("Error: Failed to parse .env file.");
}

// Define database 
define('DBNAME', $env['DBNAME']);
define('DBHOST', $env['DBHOST']);
define('DBUSER', $env['DBUSER']);
define('DBPASS', $env['DBPASS']);
define('DBDRIVER', $env['DBDRIVER'] ?? 'mysql');

// Define API keys directly from the $env array
define('OPENAI_API_KEY', $env['OPENAI_API_KEY'] ?? '');
define('CMC_API_KEY', $env['CMC_API_KEY'] ?? '');

// Define base paths
define('APP_PATH', realpath('../app'));          // Path to the app directory
define('PUBLIC_PATH', realpath('../public'));    // Path to the public directory
define('VIEW_PATH', APP_PATH . '/views');        // Path to the app views directory
define('PUBLIC_VIEW_PATH', PUBLIC_PATH . '/assets/views'); // Path to the public assets views directory

define('DEBUG', $env['DEBUG'] ?? true);

// More troubleshooting
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
?>
