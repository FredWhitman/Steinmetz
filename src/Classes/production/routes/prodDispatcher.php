<?php

$controller = require_once __DIR__ . '/../config/prodInit.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['read4wks'])) {
        $controller->read4wks();
        exit();
    }

    if (isset($_GET['viewProdLogs']) && isset($_GET['id'])) {
        $controller->viewProdLogs($_GET['id']);
        exit();
    }

    http_response_code(400);
    echo json_encode(['error' => 'Invalid GET request']);
}
