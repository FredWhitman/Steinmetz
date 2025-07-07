<?php
// File: src/Classes/production/routes/prodDispatcher.php
$controller = require_once __DIR__ . '/../config/prodInit.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    switch (true) {

        case isset($data['action']) && $data['action'] === 'addLog':
            $controller->addLog($data);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid POST request']);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    switch (true) {
        case isset($_GET['read4wks']):
            $controller->read4wks();
            break;
        case isset($_GET['viewProdLogs'], $_GET['id']):
            $controller->viewProdLogs($_GET['id']);
            break;
        case isset($_GET['action']) && $_GET['action'] === "getProducts":
            $controller->getProductList();
            break;
        case isset($_GET['action']) && $_GET['action'] === "getMaterials":
            $controller->getMaterialList();
            break;
        case isset($_GET['action']) && $_GET['action'] === "checkIfLogExists":
            $controller->checkLogDates($_GET['productID'], $_GET['date']);
            break;
        case isset($_GET['action']) && $_GET['action'] === "checkRun":
            if (isset($_GET['productID'])) {
                $controller->checkRun($_GET['productID']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Missing productID']);
            }
            break;
        case isset($_GET['action']) && in_array($_GET['action'], ['getLastLog', 'endRun']):
            if (isset($_GET['productID'])) {
                $controller->getLastLog($_GET['productID']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Missing productID']);
            }
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid GET request']);
    }
}
