<?php
// File: productionActions.php

require_once __DIR__ . '/models/ProductionModel.php';
require_once __DIR__ . '/utils/Util.php';
require __DIR__ . '/../../vendor/autoload.php'; // Adjust path as needed
require_once __DIR__ . '/../config/prodInit.php';

// Include the dispatcher to handle the request
require_once __DIR__ . '/routes/prodDispatcher.php';
