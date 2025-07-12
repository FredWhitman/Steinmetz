<?php
// FILE: LoggerTester.php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Classes/quality/Config/LogFactory.php';

use Quality\Config\LogFactory;

// Get the logger instance
$log = LogFactory::getLogger('LoggerTest');

// Trigger some test logs
$log->debug('LoggerTester: This is a debug message.');
$log->info('LoggerTester: This is an info message.');
$log->warning('LoggerTester: This is a warning.');
$log->error('LoggerTester: This is an error.');
$log->critical('LoggerTester: This is a critical error.');

echo "Logger test completed.\nCheck your logs directory for output.\n";
