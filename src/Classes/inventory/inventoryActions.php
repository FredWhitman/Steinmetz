<?php
// File: inventoryActions.php

require_once __DIR__ . '/models/InventoryModel.php';
require_once __DIR__ . '/utils/Util.php';
require __DIR__ . '/../../vendor/autoload.php'; // Adjust path as needed
require_once __DIR__ . '/../config/init.php';

// Include the dispatcher to handle the request
require_once __DIR__ . '/routes/dispatcher.php';
