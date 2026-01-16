<?php
// File: src/Classes/Inventory/Config/init_new.php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Database\Connection;
use Inventory\Config\LogFactory;
use Inventory\Models\InventoryModel;          // same class name, new architecture
use Inventory\Controllers\InventoryController_new;
use Util\Utilities;

// ---------------------------------------------------------
// Logger
// ---------------------------------------------------------
$logger = LogFactory::getLogger('Inventory_new');
error_log("✅ Logger initialized (new)");

// ---------------------------------------------------------
// Database connection
// ---------------------------------------------------------
$dbConn = new Connection();
error_log("✅ DB connected (new)");

// ---------------------------------------------------------
// Utility class
// ---------------------------------------------------------
$util = new Utilities();
error_log("✅ Util ready (new)");

// ---------------------------------------------------------
// InventoryModel (new architecture)
// ---------------------------------------------------------
$model = new InventoryModel($dbConn, $logger, $util);
error_log("✅ Model ready (new)");

// ---------------------------------------------------------
// Controller (new)
// ---------------------------------------------------------
$controller = new InventoryController_new($model, $util, $logger);
error_log("✅ Controller created (new)");

return $controller;
