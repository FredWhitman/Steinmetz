<?php
//FILE: src/Classes/quality/Routes/qaDispatcher.php;

$controller = require_once __DIR__ . '/../Config/qaInit.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    switch (true) {
        case isset($data['action']) && $data['action'] === 'addQaRejects':
            $controller->addQaRejects($data);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => "Invalid POST request."]);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    switch (true) {
        case 'GetQaLogs':
            $controller->getQALogs();
            break;

        default:
            # code...
            break;
    }
}
