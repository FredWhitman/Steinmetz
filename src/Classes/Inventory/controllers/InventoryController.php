<?php

namespace Inventory\Controllers;

// File: controllers/InventoryController.php
//require_once __DIR__ . '/../models/InventoryModel.php';

class InventoryController
{
    private $model;
    private $util;
    private $log;

    public function __construct($model, $util, $log)
    {
        $this->model = $model;
        $this->util  = $util;
        $this->log   = $log;
        $this->log->info("Controller logger test", ['file' => __FILE__]);
    }

    // GET: Retrieve inventory list
    public function getInventory()
    {
        ob_clean();
        header('Content-Type: application/json');
        $this->log->info("getInventory called");
        $inventory = $this->model->getInventory();
        echo json_encode($inventory);
        exit();
    }

    // GET: Retrieve single record for editing (e.g. editProducts, editMaterials, editPfms)
    public function getRecord()
    {
        header('Content-Type: application/json');
        if (!isset($_GET['id']) || !isset($_GET['table'])) {
            echo json_encode(["error" => "Missing required parameters"]);
            $this->log->warning("Missing parameters for getRecord");
            exit();
        }
        $id = $_GET['id'];
        $table = $_GET['table'];
        $this->log->info("getRecord called with: $id, $table");
        $record = $this->model->getRecord($id, $table);
        if (!$record) {
            echo json_encode(["error" => "Record not found!"]);
            exit();
        }
        echo json_encode($record, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit();
    }

    //GET: retrieve single record of joined tables for update (e.g. updateProducts, updateMaterials, updatePfms)
    public function getInventoryRecord()
    {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;
        $table = $_GET['table'] ?? null;

        if (!$id || !$table) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required paramaters"]);
            $this->log->warning("Missing parameters for getInventoryRecord");
            exit();
        }

        $this->log->info("getInventoryRecord called with: {$id}, {$table}");

        switch ($table) {
            case 'products':
                $record = $this->model->getInventoryRecord($id, $table);
                break;
            case 'materials':
                $record = $this->model->getInventoryRecord($id, $table);
                break;
            case 'pfms':
                $record = $this->model->getInventoryRecord($id, $table);
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid table type']);
        }

        if (!$record) {
            echo json_encode(["error" => "Record not found!"]);
            exit();
        }
        echo json_encode($record, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit();
    }

    // POST: Add Product
    public function addInventoryItem($data)
    {
        if (!isset($data['action'])) {
            http_response_code(400);
            $message = "Missing inventory item data!";
            echo $message['html'];
            return;
        }

        $item = '';

        switch ($data['action']) {
            case 'addProduct':
                $item = $data["productID"];
                break;
            case 'addMaterial':
                $item = $data["matName"];
                break;
            case 'addPFM':
                $item = $data["partName"];
                break;
            default:
                http_response_code(400);
                echo "Invalid action!";
                return;
        }

        $this->log->info("addInventoryItem called with action: " . print_r($data, true));
        $result = $this->model->addInventoryItem($data);
        $message = json_decode($this->util->showMessage(
            $result['success'] ? 'success' : 'danger',
            $result['message'] . " Inventory Item: {$item} " . ($result['success'] ? " updated!" : " failed to be updated!")
        ), true);
        echo $message['html'];
    }

    public function addShipment($data)
    {
        if (!isset($data['action'])) {
            http_response_code(400);
            $message = "Missing shipment data!";
            echo $message['html'];
            return;
        }

        $this->log->info("addShipment called with action: " . print_r($data, true));
        $result = $this->model->addShipment($data);
        $message = json_decode($this->util->showMessage(
            $result['success'] ? 'success' : 'danger',
            $result['message'] . " Shipment for Product ID: {$data['productID']} " . ($result['success'] ? " added!" : " failed to be added!")
        ), true);
        echo $message['html'];
    }

    // POST: Edit product
    public function editProduct($data)
    {
        if (!isset($data["products"])) {
            http_response_code(400);
            echo "Missing product data!";
            return;
        }
        $result = $this->model->editInventory($data);
        $message = json_decode($this->util->showMessage(
            $result['success'] ? 'success' : 'danger',
            $result['message'] . " Product ID: {$data['products']['productID']} " . ($result['success'] ? " updated!" : " failed to be updated!")
        ), true);
        echo $message['html'];
    }

    // POST: Edit material
    public function editMaterial($data)
    {
        if (!isset($data["materials"])) {
            http_response_code(400);
            echo "Missing material data!";
            return;
        }
        $result = $this->model->editInventory($data);
        $message = json_decode($this->util->showMessage(
            $result['success'] ? 'success' : 'danger',
            $result['message'] . " Material: {$data['materials']['matPartNumber']} " . ($result['success'] ? " updated!" : " failed to be updated!")
        ), true);

        echo $message['html'];
    }

    // POST: Edit PFM
    public function editPFM($data)
    {
        if (!isset($data["pfm"])) {
            http_response_code(400);
            echo "Missing PFM data!";
            return;
        }

        $result = $this->model->editInventory($data);
        $this->log->info("editPFM result: " . print_r($result, true));
        $message = json_decode($this->util->showMessage(
            $result['success'] ? 'success' : 'danger',
            $result['message'] . " PFM: {$data['pfm']['partName']} " . ($result['success'] ? "updated!" : "failed to be updated!")
        ), true);
        echo $message['html'];
    }

    public function updateProduct($data)
    {
        $this->log->info("POST Data Received by controller:\n" . print_r($data, true));
        if (!isset($data['action'])) {
            http_response_code(400);
            echo "Missing UpdateProduct Data!";
            return;
        }

        $result = $this->model->updateInvQty($data);
        $message = json_decode($this->util->showMessage(
            $result['success'] ? 'success' : 'danger',
            $result['message'] . " Product {$data['productID']}" . ($result['success'] ? " was updated!" : "failed to be updated!")
        ), true);
        echo $message['html'];
    }

    public function updateMaterial($data)
    {
        $this->log->info("POST Data Received by controller:\n" . print_r($data, true));
        if (!isset($data['action'])) {
            http_response_code(400);
            echo "Missing UpdateMaterial Data!";
            return;
        }
        $this->log->info("updateMaterial called for: {$data['matPartNumber']}");

        $result = $this->model->updateInvQty($data);
        $message = json_decode($this->util->showMessage(
            $result['success'] ? 'success' : 'danger',
            $result['message'] . " Material {$data['matPartNumber']}" . ($result['success'] ? " was updated!" : "failed to be updated!")
        ), true);
        echo $message['html'];
    }

    public function updatePfm($data)
    {
        $this->log->info("POST Data Received by controller:\n" . print_r($data, true));
        if (!isset($data['action'])) {
            http_response_code(400);
            echo "Missing UpdatePfm Data!";
            return;
        }

        $result = $this->model->updateInvQty($data);
        $message = json_decode($this->util->showMessage(
            $result['success'] ? 'success' : 'danger',
            $result['message']  . " PFM {$result['pfm']} " . ($result['success'] ? " was updated!" : "failed to be updated!")
        ), true);
        echo $message['html'];
    }

    /**
     * getProductList function return an array of productIDs and partNames
     *
     * @return void  a list of products
     */
    public function getProductList()
    {
        header('Content-Type: application/json');

        try {
            $products = $this->model->getProductList();
            echo json_encode($products);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch product list', 'details' => $e->getMessage()]);
        }
    }

    public function getShipments()
    {
        header('Content-Type: application/json');

        try {
            $shipments = $this->model->getShipments();
            echo json_encode($shipments);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch shipments', 'details' => $e->getMessage()]);
        }
    }
}
