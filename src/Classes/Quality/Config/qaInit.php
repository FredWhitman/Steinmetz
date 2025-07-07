<?php
//FILE: src/Classes?Quality/Config/qaInity.php

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Database\Connection;
use Quality\Config\LogFactory;
use Quality\Models\QualityModel;
use Quality\Controllers\QualityController;

$logger = LogFactory::getLogger('Quality');

$dbConn = new Connection();

$model = new QualityModel($dbConn, $logger);

