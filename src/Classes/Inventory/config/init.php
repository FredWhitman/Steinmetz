<?php
//File: src/Classes/inventory/config/init.php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Database\Connection;
use Inventory\Config\LogFactory;
use Inventory\Models\InventoryModel;
use Inventory\Controllers\InventoryController;
use Util\Utilities;

$logger = LogFactory::getLogger('Inventory');
error_log("✅ Logger initialized");

//create db connection
$dbConn = new Connection();
error_log("✅ DB connected");

//create util
$util = new Utilities;
error_log("✅ Util ready");

//initialize InventoryModel (creates DB and logger)
$model = new InventoryModel($dbConn, $logger, $util);
error_log("✅ Model ready");

$controller = new InventoryController($model, $util, $logger);
error_log("✅ Controller created");

return $controller;
