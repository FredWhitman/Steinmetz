<?php
// File: routes/dispatcher.php
$controller = require_once __DIR__ . '/../Config/init.php';
/* require_once __DIR__ . '/../controllers/InventoryController.php'; */

$data = json_decode(file_get_contents('php://input'), true);

// Dispatcher for POST actions – based on the "action" variable in the JSON payload.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $logger->info("POST Data Received by dispatcher:\n" . print_r($data, true));

    $routes = [
        'editProduct'  => [$controller, 'editProduct'],
        'editMaterial' => [$controller, 'editMaterial'],
        'editPFM'      => [$controller, 'editPFM'],
        'updateProduct' => [$controller, 'updateProduct'],
        'updateMaterial' => [$controller, 'updateMaterial'],
        'updatePfm' => [$controller, 'updatePfm']
        // Add additional POST action routes as needed.  
    ];

    $action = $data['action'] ?? null;
    if ($action && isset($routes[$action])) {
        call_user_func($routes[$action], $data);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or missing action']);
    }
    exit();
}

// Dispatcher for GET requests.
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // We can distinguish based on query parameters.
    if (isset($_GET['getInventory'])) {
        $controller->getInventory();
    } elseif (isset($_GET['editProducts']) || isset($_GET['editMaterials']) || isset($_GET['editPfms'])) {
        $controller->getRecord();
    } elseif (isset($_GET['updateProducts']) || isset($_GET['updateMaterials']) || isset($_GET['updatePfms'])) {
        $controller->getInventoryRecord();
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid GET request']);
    }
    exit();
}
