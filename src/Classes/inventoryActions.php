<?php
require_once 'inventoryDB_SQL.php';
require __DIR__ . '/../../vendor/autoload.php';
require_once 'util.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\ErrorHandler;
use Psr\Log\LogLevel; // You can import LogLevel for clarity, or use string constants


$db = new inventoryDB_SQL;
$util = new Util;

// Create your Monolog Logger instance with handlers
$log = new Logger('inventoryActionErrors');

// Example: Log all errors to a file
$log->pushHandler(new StreamHandler('logs/inventory_errors.log', Logger::DEBUG)); // Log everything from DEBUG level

// Register the Monolog ErrorHandler
ErrorHandler::register($log);

//gets a current list of parts, material and pfms from database
if (isset($_GET['getInventory'])) {
    header('Content-Type: application/json');
    $log->error('Testing inventoryActions.php error & log handling: getInventory was called!');

    $inventory = $db->getInventory();
   
    //Output the entire associative array as JSOn
    echo json_encode($inventory);

    exit();
}
