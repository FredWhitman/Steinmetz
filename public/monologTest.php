<?php

require __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\ErrorHandler;
use Psr\Log\LogLevel; // You can import LogLevel for clarity, or use string constants

// 1. Create your Monolog Logger instance with handlers
$log = new Logger('my_app_errors');

// Example: Log all errors to a file
$log->pushHandler(new StreamHandler('logs/app_errors.log', Logger::DEBUG)); // Log everything from DEBUG level

// Example: Send critical errors via email (you'd configure this properly with mailer settings)
// $log->pushHandler(new Monolog\Handler\NativeMailerHandler(
//     'your_email@example.com',
//     'Critical Error on My App',
//     'noreply@example.com',
//     Logger::CRITICAL // Only send emails for critical errors and above
// ));

// 2. Register the Monolog ErrorHandler
ErrorHandler::register($log);

// Optional: Customize error level mapping (PHP error type to Monolog log level)
// By default, Monolog maps common PHP errors to appropriate log levels.
// You can override this if needed.
/*
$errorHandler = new ErrorHandler($log);
$errorHandler->registerErrorHandler([
    E_NOTICE => LogLevel::INFO, // Treat notices as INFO logs
    E_WARNING => LogLevel::WARNING,
    E_DEPRECATED => LogLevel::DEBUG // Treat deprecations as DEBUG logs
], true); // The 'true' means it will also call the previous error handler (if any)
$errorHandler->registerExceptionHandler(); // Register exception handler
$errorHandler->registerFatalHandler();     // Register fatal error handler
*/

// --- Now, let's test it out ---

echo "Starting application...\n";

// Test a PHP Notice
trigger_error("This is a test notice from my application.", E_USER_NOTICE);

// Test a PHP Warning
trigger_error("This is a test warning, something might be wrong!", E_USER_WARNING);

// Test an uncaught exception
try {
    throw new \Exception("Something unexpected happened!");
} catch (\Exception $e) {
    // You can manually log caught exceptions if you want more control over context
    $log->error("Caught Exception: " . $e->getMessage(), ['exception' => $e]);
}

// Now, let's trigger an uncaught exception (this will be caught by Monolog's handler)
// This line will cause the script to stop after Monolog logs it.
// Uncomment to test:
// throw new \RuntimeException("This is an uncaught runtime exception!");

// Test a fatal error (this typically needs to be outside a try-catch and occur during execution)
// For example, calling a non-existent function or class might lead to a fatal error.
// Uncomment to test:
// nonExistentFunction();

echo "Application finished.\n";
