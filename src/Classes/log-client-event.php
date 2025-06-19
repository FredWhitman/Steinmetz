<?php
require __DIR__ . '/../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FilterHandler;

$logger = new Logger('JS_InventoryController_Console-Log');

$infoStream = new StreamHandler(__DIR__ . '/logs/inventory_info.log', Logger::INFO);
$infoHandler = new FilterHandler($infoStream, Logger::INFO, Logger::NOTICE);

$errorStream = new StreamHandler(__DIR__ . '/logs/inventory_errors.log', Logger::ERROR );
$errorHandler = new FilterHandler($errorStream, Logger::ERROR);

$logger->setHandlers([$infoHandler, $errorHandler]);

$data = json_decode(file_get_contents("php://input"), true);
$message = $data['message'] ?? 'No message';
$context = $data['context'] ?? [];

$logger->info($message, $context);

echo json_encode(['status' => 'logged']);