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
$log->pushHandler(new StreamHandler('logs/inventory_errors.log', Logger::ERROR)); // Log everything from DEBUG level


// Register the Monolog ErrorHandler
ErrorHandler::register($log);

//gets a current list of parts, material and pfms from database
if (isset($_GET['getInventory'])) {
    header('Content-Type: application/json');
    $log->info('Testing inventoryActions.php error & log handling: getInventory was called!');

    $inventory = $db->getInventory();

    //Output the entire associative array as JSOn
    echo json_encode($inventory);

    exit();
}

//Handle Edit product Ajax request from main.js editProduct
if (isset($_GET['editProducts'])) {

    header('Content-Type: application/json');
    if (!isset($_GET['id']) || !isset($_GET['table'])) {
        echo json_encode(["error" => "Missing required parameters"]);
        $log->warning("No required paramaters!");
        exit();
    }

    $log->info('editProduct called with this data: ' . $_GET['id'] . ' ' . $_GET['table']);
    $id = $_GET['id'];
    $table = $_GET['table'];
    $record = $db->getRecord($id, $table);

    if (!$record) {
        echo json_encode(["error" => "Record not found!"]);
        exit();
    }

    echo json_encode($record, JSON_FORCE_OBJECT);

    exit();
}

//Handle Edit material Ajax request from inventoryController.js editMaterial
if (isset($_GET['editMaterials'])) {

    header('Content-Type: application/json');
    if (!isset($_GET['id']) || !isset($_GET['table'])) {
        echo json_encode(["error" => "Missing required parameters"]);
        $log->warning("No required paramaters!");
        exit();
    }

    $log->info('editMaterial called with this data: ' . $_GET['id'] . ' ' . $_GET['table']);
    $id = $_GET['id'];
    $table = $_GET['table'];
    $record = $db->getRecord($id, $table);

    if (!$record) {
        echo json_encode(["error" => "Record not found!"]);
        exit();
    }

    echo json_encode($record, JSON_FORCE_OBJECT);
    exit();
}

if (isset($_GET['editPfms'])) {
    header('Content-Type: application/json');
    if (!isset($_GET['id']) || !isset($_GET['table'])) {
        echo json_encode(["error" => "Missing required parameters"]);
        $log->warning("No required paramaters!");
        exit();
    }

    $log->info('editPFMs called with this data: ' . $_GET['id'] . ' ' . $_GET['table']);
    $id = $_GET['id'];
    $table = $_GET['table'];
    $record = $db->getRecord($id, $table);

    if (!$record) {
        echo json_encode(["error" => "Record not found!"]);
        exit();
    }

    echo json_encode($record, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit();
}
