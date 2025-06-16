<?php
require_once 'inventoryDB_SQL.php';
require __DIR__ . '/../../vendor/autoload.php';
require_once 'util.php';


use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Psr\Log\LogLevel; // You can import LogLevel for clarity, or use string constants


$db = new inventoryDB_SQL;
$util = new Util;

// Create your Monolog Logger instance with handlers
$log = new Logger('inventoryActionErrors');

// Example: Log all errors to a file
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/inventory_errors.log', Logger::DEBUG)); // Log everything from DEBUG level


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

//Handle Edit product Ajax request from inventoryController.js editProduct
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

//Handle Edit pfm Ajax request from inventoryController.js editPFM
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

//Handles Ajax call to edit inventory items
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    //log data sent from form
    $log->info("Posted data sent to handler " . "\n" . print_r($data, true));


    if (isset($data["action"]) && $data["action"] === "editProduct") {
        $log->info("editProduct was present in data!");

        if (isset($data["products"])) {

            $result = $db->editInventory($data);

            $log->info("update status: " . print_r($result, true));

            if ($result["success"]) {
                echo $util->showMessage('success', $result['message'] . " " . "Product ID: {$result['product']} has been updated!");
            } else {
                echo $util->showMessage('danger', 'Failed to update product details.');
            }
        } else {
            echo "Missing required data! Failed to pass log data!";
            http_response_code(400);
        }
    } else if (isset($data["action"]) && $data["action"] === "editMaterial") {
        if (isset($data["materials"])) {
            $result = $db->editInventory($data);

            $log->info("update status: " . print_r($result, true));

            if ($result["success"]) {
                echo $util->showMessage('success', $result['message'] . ' ' . "Material: {$result['material']} has been updated!");
            } else {
                echo $util->showMessage('danger', $result['message'] . ' ' . $result['error']);
            }
        } else {
            echo "Missing required data! Failed to pass log data!";
            http_response_code(400);
        }
    } else if (isset($data["action"]) && $data["action"] === "editPFM") {
        if (isset($data["pfm"])) {
            $result = $db->editInventory($data);
            $log->info("update status: " . print_r($result, true));
            if ($result['success']) {
                echo $util->showMessage('success', $result['message'] . ' ' . "PFM: {$result['pfm']} has been updated!");
            } else {
                echo $util->showMessage('danger', $result['message'] . ' ' . $result['error']);
            }
        } else {
            echo "Missing required data! Failed to pass log data!";
            http_response_code(400);
        }
    } else {
        echo "Unauthorized request!";
        http_response_code(403); // Forbidden status
    }
}
