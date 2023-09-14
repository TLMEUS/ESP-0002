<?php
/**
 * This file contains API/bootstrap.php for project ESP-0002
 *
 * File Information
 * Project Name: ESP-0002
 * Model Name: API
 * File Name: bootstrap.php
 * File Version: 1.10.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Last Modified: 08/31/2023
 * File Authored on: 03/24/2023
 * File Copyright: 03/2023
 */
namespace {

    /**
     * Configure Composer Autoload
     */
    require dirname(path: __DIR__) . "/vendor/autoload.php";

    /**
     *  Set the error handler
     */
    set_error_handler(callback: "Handlers\\Error::handleError");

    /**
     * Set the exception handler
     */
    set_exception_handler(callback: "Handlers\\Error::handleException");

    /**
     * Setup to .dotenv object
     */
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(path: __DIR__));

    /**
     * Load the environment settings
     */
    $dotenv->load();

    /**
     * Set the content-type header
     */
    header(header: "Content-type: application/json; charset=UTF-8");
}