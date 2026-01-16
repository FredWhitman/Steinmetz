<?php

namespace Inventory\Controllers;

use Inventory\Models\InventoryGet;
use Inventory\Models\InventoryInsert;
use Inventory\Models\InventoryUpdate;

class InventoryController_new
{

    private $model;
    private $util;
    private $log;


    public function __construct($model, $util, $log)
    {
        $this->model = $model;
        $this->util = $util;
        $this->log = $log;
    }

    public function getInventory() {}
    public function getProducts() {}
    public function getShipments() {}
    public function getRecordForEdit($id, $table) {}
    public function getRecordForUpdate($id, $table) {}

    public function editProduct($input) {}
    public function editMaterial($input) {}
    public function editPFM($input) {}

    public function updateProduct($input) {}
    public function updateMaterial($input) {}
    public function updatePFM($input) {}
}
