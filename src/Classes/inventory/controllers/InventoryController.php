<?php

namespace Inventory\Controllers;

// File: controllers/InventoryController.php
require_once __DIR__ . '/../models/InventoryModel.php';

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
    public function getInventoryRecord(){
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? null;
        $table = $_GET['table'] ?? null;

        if(!$id || !$table){
            http_response_code(400);
            echo json_encode(["error" => "Missing required paramaters"]);
            $this->log->warning("Missing parameters for getInventoryRecord");
            exit();
        }
        
        $this->log->info("getInventoryRecord called with: {$id}, {$table}");

        switch ($table){
            case 'products':
                $record = $this->model->getInventoryRecord($id,$table);
                break;
            case 'materials':
                $record = $this->model->getInventoryRecord($id,$table);
                break;
            case 'pfms':
                $record = $this->model->getInventoryRecord($id,$table);
                break;
            default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid table type']);
        }
        
        if(!$record){
            echo json_encode(["error" => "Record not found!"]);
            exit();
        }
        echo json_encode($record , JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit();
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
        $this->log->info("editProduct result: " . print_r($result, true));
        if ($result["success"]) {
            echo $this->util->showMessage('success', $result['message'] . " Product ID: {$result['product']} updated!");
        } else {
            echo $this->util->showMessage('danger', 'Failed to update product details.');
        }
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
        $this->log->info("editMaterial result: " . print_r($result, true));
        if ($result["success"]) {
            echo $this->util->showMessage('success', $result['message'] . " Material: {$result['material']} updated!");
        } else {
            echo $this->util->showMessage('danger', $result['message'] . ' ' . $result['error']);
        }
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
        if ($result["success"]) {
            echo $this->util->showMessage('success', $result['message'] . " PFM: {$result['pfm']} updated!");
        } else {
            echo $this->util->showMessage('danger', $result['message'] . ' ' . $result['error']);
        }
    }
    
    public function updateProduct($data){
        $this->log->info("POST Data Received by controller:\n" . print_r($data, true));
        if(!isset($data['action'])){
            http_response_code(400);
            echo "Missing UpdateProduct Data!";
            return;
        }

        $result = $this->model->updateInvQty($data);
        if(!$result['success']){
            echo $this->util->showMessage('danger', $result['message'] . " updateInvqty: {$data['action']} failed to be updated!");
        }else{
            echo $this->util->showMessage('success', $result['message'] . " updateInvQty: {$data['action']} was successful");
        }
    }

    public function updateMaterial($data){
        $this->log->info("POST Data Received by controller:\n" . print_r($data, true));
        if(!isset($data['action'])){
            http_response_code(400);
            echo "Missing UpdateProduct Data!";
            return;
        }

        $result = $this->model->updateInvQty($data);
        if(!$result['success']){
            echo $this->util->showMessage('danger', $result['message'] . " updateInvqty: {$data['action']} failed to be updated!");
        }else{
            echo $this->util->showMessage('success', $result['message'] . " updateInvQty: {$data['action']} was successful");
        }
    }
    // Additional methods (e.g., updateProduct or deleteItem) go here...
}
