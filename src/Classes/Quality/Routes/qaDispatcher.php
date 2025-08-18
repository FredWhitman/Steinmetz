<?php
//FILE: src/Classes/quality/Routes/qaDispatcher.php;

$controller = require_once __DIR__ . '/../Config/qaInit.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    switch (true) {
        case isset($data['action']) && $data['action'] === 'addQaRejects':
            $controller->addQaRejects($data);
            break;
        case isset($data['action']) && $data['action'] === 'addLotChange':
            $controller->addLotChange($data);
            break;
        case isset($data['action']) && $data['action'] === 'addOvenLog':
            $controller->addOvenLog($data);
            break;
        case isset($data['action']) && $data['action'] === 'updateOvenLog':
            $controller->updateOvenLog($data);
            break;
        case isset($data['action']) && $data['action'] === 'matReceived':
            $controller->addMatTransaction($data);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => "Invalid POST['action'] request."]);
    }
}


if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['action'])) {

    switch ($_GET['action']) {
        case 'getQaLogs':
            $controller->getQALogs();
            break;
        case 'getProducts':
            $logger->info('getProducts called from qaDispatcher.', ['source' => 'QualityController.php']);
            $controller->getProductList();
            break;
        case 'getMaterials':
            $controller->getMaterialList();
            break;
        case 'getQaRejectLog':
            $controller->getQaRejectLog($_GET['id']);
            break;
        case 'getOvenLog':
            $controller->getOvenLog($_GET['id']);
            break;
        case 'getLotChangeLog':
            $controller->getLotChange($_GET['id']);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid GET action.']);
            break;
    }
}
