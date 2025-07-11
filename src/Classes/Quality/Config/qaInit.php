<?php
//FILE: src/Classes?Quality/Config/qaInit.php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Database\Connection;
use Quality\Config\LogFactory;
use Quality\Models\QualityModel;
use Quality\Controllers\QualityController;
use Util\Utilities;

$logger = LogFactory::getLogger('Quality');

$util = new Utilities();

$dbConn = new Connection();

$model = new QualityModel($dbConn, $logger, $util);

$controller = new QualityController($model, $util, $logger);

return $controller;
