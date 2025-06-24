<?php
// File: src/Classes/inventory/config/LogFactory.php

namespace Inventory\Config;

use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;

class LogFactory
{
    // This static property will hold our single logger instance.
    private static $logger = null;

    /**
     * Returns a configured logger instance.
     * If one does not exist yet, it creates it.
     *
     * @param string $name The name/identifier of the logger.
     * @return Logger
     */
    public static function getLogger($name = 'Application')
    {
        // If we haven't created a logger yet, do so.
        if (self::$logger === null) {
            self::$logger = new Logger($name);

            // Configure handlers.
            // Adjust the paths as needed relative to this file.
            self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/inventory_errors.log', Logger::ERROR));
            self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/inventory_Info.log', Logger::INFO));

            // Register the logger as the error handler for PHP errors.
            ErrorHandler::register(self::$logger);
        }

        // Optionally, you can update the logger's name if needed.
        // But note that doing so may affect already-set handlers.
        return self::$logger;
    }
}
