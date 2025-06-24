<?php
// File: routes/prodDispatcher.php
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../controllers/ProductionController.php';

//Dispatcher for GET requests

if ($_SERVER["REQUEST_METHOD" === "GET"]) {
    if(isset($_GET['read4wks'])){
        $controller->read4wks();
    }else{
        http_response_code(400);
         echo json_encode(['error' => 'Invalid GET request']);
    }
    exit();
}