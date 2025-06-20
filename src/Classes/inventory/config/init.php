<?php
//File: src/Classes/inventory/config/init.php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../models/InventoryModel.php';
require_once __DIR__ . '/../utils/Util.php';
require_once __DIR__ . '/../controllers/InventoryController.php';
require_once __DIR__ . '/LogFactory.php';

use Inventory\Config\LogFactory;
use Inventory\Model\InventoryModel;
use Inventory\Controllers\InventoryController;


$logger = LogFactory::getLogger('InventoryApp');

//create db connection
$database = new database();
$db = $database->dbConnection();

//initialize InventoryModel (creates DB and logger)
$model = new InventoryModel($db, $logger);

//create util
$util = new Util();

$controller = new InventoryController($model, $util, $logger);
