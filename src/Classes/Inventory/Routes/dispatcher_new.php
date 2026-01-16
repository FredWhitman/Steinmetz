<?php
//FILE /src/Classes/Inventory/Routes/dispatcher_new.php

header("Content-Type: application/json");

// Load the NEW Inventory controller (fully constructed) 
$controller = require_once __DIR__ . '/../Config/init_new.php';

// Read action
$action = $_GET['action'] ?? null;

//Read POST body (JSON)
$input = json_decode(file_get_contents("php://input"), true) ?? [];

//Unified JSON response helper.
function sendResponse($success, $type, $message, $html = "", $data = null)
{
    echo json_encode([
        "success" => $success,
        "type" => $type,
        "message" => $message,
        "html" => $html,
        "data" => $data
    ]);
    exit;
}

//No action provided
if (!$action) {
    sendResponse(false, "danger", "No action specified.");
}

try {
    switch ($action) {
        /* -------------------------------------------------
            GET REQUESTS
        --------------------------------------------------*/
        case "getInventory":
            $result = $controller->getInventory();
            sendResponse(true, "success", "Inventory loaded.", "", $result);
        case "getProducts":
            $result = $controller->getProducts();
            sendResponse(true, "success", "Products loaded.", "", $result);
        case "getShipments":
            $result = $controller->getShipments();
            sendResponse(true, "success", "Shipments loaded.", "", $result);
        case "getRecordForEdit":
            $id = $_GET["id"] ?? null;
            $table = $_GET["table"] ?? null;
            $result = $controller->getRecordForEdit($id, $table);
            sendResponse(true, "success", "Record loaded.", "", $result);
        case "getRecorForUpdate":
            $id = $_GET["id"] ?? null;
            $table = $_GET["table"] ?? null;
            $result = $controller->getRecordForUpdate($id, $table);
            sendResponse(true, "success", "Record loaded.", "", $result);
            /*---------------------------------------------------
            POST REQUESTS - EDIT
        ---------------------------------------------------*/
        case "editProduct":
            $result = $controller->editProduct($input);
            sendResponse($result["success"], $result["type"], $result["message"], $result["html"], $result["data"] ?? null);
        case "editMaterial":
            $result = $controller->editMaterial($input);
            sendResponse($result["success"], $result["type"], $result["message"], $result["html"], $result["data"] ?? null);
        case "editPFM":
            $result = $controller->editPFM($input);
            sendResponse($result["success"], $result["type"], $result["message"], $result["html"], $result["data"] ?? null);
            /*---------------------------------------------------
            POST REQUESTS - UPDATE (Qty changes)
        ---------------------------------------------------*/
        case "updateProduct":
            $result = $controller->updateProduct($input);
            sendResponse($result["success"], $result["type"], $result["message"], $result["html"], $result["data"] ?? null);
        case "updateMaterial":
            $result = $controller->updateMaterial($input);
            sendResponse($result["success"], $result["type"], $result["message"], $result["html"], $result["data"] ?? null);
        case "updatePFM":
            $result = $controller->updatePFM($input);
            sendResponse($result["success"], $result["type"], $result["message"], $result["html"], $result["data"] ?? null);
        default:
            sendResponse(false, "danger", "Unknown action: $action");
    }
} catch (Exception $e) {
    sendResponse("false", "danger", "Server error: " . $e->getMessage());
}
