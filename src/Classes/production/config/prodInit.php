<?php
//File: src/Classes/production/config/prodInit.php

error_log("🛬 Entered prodInit.php");

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../models/ProductionModel.php';
require_once __DIR__ . '/../utils/Util.php';
require_once __DIR__ . '/../controllers/ProductionController.php';
require_once __DIR__ . '/LogFactory.php';

use Production\Config\LogFactory;
use Production\Models\ProductionModel;
use Production\Controllers\ProductionController;
use Production\utils\Util;

$logger = LogFactory::getLogger('Production');
error_log("✅ Logger initialized");

$database = new database();
$db = $database->dbConnection();
error_log("✅ DB connected");

$util = new Util();
error_log("✅ Util ready");

$model = new ProductionModel($db, $logger, $util);
error_log("✅ Model ready");

$controller = new ProductionController($model, $util, $logger);
error_log("✅ Controller created");

return $controller;
