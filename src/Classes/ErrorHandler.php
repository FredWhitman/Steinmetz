<?php

/**
 * A global error and exception handler.
 * This class centralizes error logging and response formatting.
 */
class ErrorHandler
{

    /**
     * Registers custom handlers for errors, exceptions, and shutdown events.
     */
    public static function register()
    {
        // Custom error handler for standard PHP errors.
        set_error_handler([__CLASS__, 'handleError']);

        // Custom exception handler for uncaught exceptions.
        set_exception_handler([__CLASS__, 'handleException']);

        // Custom shutdown function to catch fatal errors.
        register_shutdown_function([__CLASS__, 'handleShutdown']);
    }

    /**
     * Handles PHP errors.
     *
     * @param int    $errno   The error number.
     * @param string $errstr  The error message.
     * @param string $errfile The filename where the error occurred.
     * @param int    $errline The line number of the error.
     *
     * @throws ErrorException Converts the error into an exception.
     */
    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        // Log the error details in the server's error log.
        error_log("Error [$errno]: $errstr in $errfile on line $errline");

        // Convert the error into an exception so it can be handled uniformly.
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Handles uncaught exceptions.
     *
     * @param Throwable $exception The uncaught exception.
     */
    public static function handleException($exception)
    {
        // Log detailed exception information.
        error_log("Uncaught Exception: " . $exception->getMessage() .
            " in " . $exception->getFile() .
            " on line " . $exception->getLine());

        // If headers haven't been sent, you can set the appropriate content type.
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }

        // Return a consistent JSON formatted error response.
        $response = [
            'error'   => true,
            'message' => 'An unexpected error occurred. Please try again later.'
        ];

        // You could also include additional details, such as an error code,
        // but be mindful of exposing sensitive info in production.
        echo json_encode($response);
        exit(1);
    }

    /**
     * Handles fatal errors during script shutdown.
     * This function is used to detect fatal errors that are not caught by the normal error handler.
     */
    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error !== null) {
            // Create an ErrorException from the shutdown error.
            $exception = new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            // Delegate handling of the exception to our exception handler.
            self::handleException($exception);
        }
    }
}
