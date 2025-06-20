<?php
//File: src/Classes/inventory/config/init.php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../models/InventoryModel.php';
require_once __DIR__ . '/../utils/Util.php';
require_once __DIR__ . '/../controllers/InventoryController.php';

//initialize InventoryModel (creates DB and logger)
$model = new InventoryModel();
$util = new Util();
$controller = new InventoryModel($model,$util,$model->getLogger());


