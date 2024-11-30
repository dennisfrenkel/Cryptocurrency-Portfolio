<?php
namespace app\core;

class Logger {
    public static function logError($message) {
        $logFile = '../logs/errors.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
    }
}
?>
