<?php

namespace app\controllers;

abstract class Controller {


    public function returnView($pathToView, $usePublicPath = false) {
        // Determine whether to use the public path or the default view path based on the $usePublicPath flag.
        $basePath = $usePublicPath ? PUBLIC_VIEW_PATH : VIEW_PATH;
        $fullPath = $basePath . '/' . ltrim($pathToView, '/');  // Construct the full file path for the view.
    
        // Check if the view file exists. If not, output an error message.
        if (!file_exists($fullPath)) {
            // Get backtrace information to see where the function was called from.
            $backtrace = debug_backtrace();
            $caller = $backtrace[1]; // The previous function that called returnView.

            // Construct a detailed error message about the missing view file.
            $errorMessage = "Error: View not found - {$fullPath}\n";
            $errorMessage .= "Called in: {$caller['file']} on line {$caller['line']}\n";
            $errorMessage .= "Calling function: {$caller['function']}";

            // Output the error message and terminate the script.
            die(nl2br($errorMessage));
        }
    
        // If the view file exists, include it and terminate the script.
        require $fullPath;
        exit();
    }

    public function returnJSON($json) {
        // Set the response content type to JSON.
        header("Content-Type: application/json");

        // Convert the $json array to a JSON string and output it.
        echo json_encode($json);

        // Terminate the script
        exit();
    }

    public function isAuthenticated() {
        // Check if the user is authenticated by looking for a valid user ID in the session.
        if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
            return (int) $_SESSION['user_id'];  // Return the user's ID if they are logged in.
        }

        // If the user is not authenticated, return a JSON error message and stop the execution.
        $this->returnJSON(['status' => 'error', 'message' => 'Unauthorized'], 401);
    }
}
