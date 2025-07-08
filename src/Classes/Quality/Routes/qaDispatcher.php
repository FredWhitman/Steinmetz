<?php
//FILE: src/Classes/quality/Routes/qaDispatcher.php;

$controller = require_once __DIR__ . '/../Config/qaInit.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    switch (true) {
        case 'value':
            # code...
            break;

        default:
            # code...
            break;
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
