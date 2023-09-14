<?php
/**
 * This file contains Src/Gateways/User.php for project ESP-0002
 *
 * File Information
 * Project Designation: ESP-0002
 * Model Name: Src/Gateways
 * File Name: User.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Last Modified: 08/30/2023
 * File Authored on: 03/14/2023
 * File Copyright: 03/2023
 */
namespace Handlers;

/**
 * Import required classes
 */
use ErrorException;
use Throwable;

/**
 * Error Handler class
 */
class Error {

    /**
     * handleError Method
     *
     * This method turns an error into an exception, for the exception handler to handle it.
     *
     * @param int $errno The error number
     * @param string $errstr The error text
     * @param string $errfile The file the error is in
     * @param int $errline The line number containing the error
     * @return void
     * @throws ErrorException
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): void {
        throw new ErrorException(message: $errstr, code: 0, severity: $errno, filename: $errfile, line: $errline);
    }

    /**
     * handleException static method
     *
     * This static method handles an exception by throwing an exception in error data to be displayed in JSON format.
     *
     * @param Throwable $exception
     * @return void
     */
    public static function handleException(Throwable $exception): void {

        http_response_code(response_code: 500);
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }
}