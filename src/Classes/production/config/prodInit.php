<?php
//File: src/Classes/production/config/prodInit.php

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

$database = new database();
$db = $database->dbConnection();

$util = new Util();

$model = new ProductionModel($db, $logger, $util);

