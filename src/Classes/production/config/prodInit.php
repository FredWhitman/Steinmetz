<?php
//File: src/Classes/production/config/prodInit.php

error_log("🛬 Entered prodInit.php");

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Database\Connection;
use Production\Config\LogFactory;
use Production\Models\ProductionModel;
use Production\Controllers\ProductionController;
use Util\Utilities;

$logger = LogFactory::getLogger('Production');
error_log("✅ Logger initialized");

$dbConn = new Connection();
error_log("✅ DB connected");

$util = new Utilities;
error_log("✅ Util ready");

$model = new ProductionModel($dbConn, $logger, $util);
error_log("✅ Model ready");

$controller = new ProductionController($model, $util, $logger);
error_log("✅ Controller created");

return $controller;
