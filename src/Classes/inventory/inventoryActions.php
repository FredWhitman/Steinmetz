<?php
// File: inventoryActions.php

require_once __DIR__ . '/models/InventoryModel.php';
require_once __DIR__ . '/utils/Util.php';
require __DIR__ . '/../../vendor/autoload.php'; // Adjust path as needed

use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;

// Initialize your inventory model, utilities, and logger
$db = new InventoryModel;
$util = new Util;

$log = new Logger('inventoryActionErrors');
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/inventory_errors.log', Logger::DEBUG));
ErrorHandler::register($log);

// Include the dispatcher to handle the request
require_once __DIR__ . '/routes/dispatcher.php';
