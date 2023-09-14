<?php
/**
 * This file contains API/index.php for project ESP-0002
 *
 * File Information
 * Project Designation: ESP-0002
 * Model Name: API
 * File Name: index.php
 * File Version: 1.1.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Last Modified: 08/31/2023
 * File Authored on: 03/15/2023
 * File Copyright: 03/2023
 */
declare(strict_types=1);

/**
 * Load needed classes
 */
use Controllers\Addon as AddonController;
use Controllers\Category as CategoryController;
use Controllers\Plan as PlanController;
use Database\Database;
use Gateways\Addon as AddonGateway;
use Gateways\Category as CategoryGateway;
use Gateways\Plan as PlanGateway;

/**
 * Load the bootstrap file
 */
require __DIR__ . "/bootstrap.php";

/**
 * Get the path from the URI
 */
$path = parse_url($_SERVER["REQUEST_URI"], component: PHP_URL_PATH);

/**
 * Get the request method
 */
$method = $_SERVER["REQUEST_METHOD"];

/**
 * Break the path apart
 */
$parts = explode(separator: "/", string: $path);

/**
 * Assign part one to the resource
 */
$resource = $parts[1];

/**
 * Determine $id, $category, and $addon values dependent on the value of $resource
 */
if($resource == "category") {
    $colId = $parts[2] ?? null;
}
if($resource == "plan") {
    $colPid = $parts[2];
    $colId = $parts[3] ?? null;
}
if($resource == "addon") {
    $colPid = $parts[2];
    $colId = $parts[3] ?? null;
}

/**
 * Create the database object
 */
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

/**
 * Prepare the request
 */
switch ($resource) {
    case "category":
        $controller_gateway = new CategoryGateway($database);
        $controller = new CategoryController($controller_gateway);
        $controller->processRequest(method: $method, colId: $colId);
        break;
    case "plan":
        $controller_gateway = new PlanGateway($database);
        $controller = new PlanController($controller_gateway);
        $controller->processRequest(method: $method, colId: $colId, colPid: $colPid);
        break;
    case "addon":
        $controller_gateway = new AddonGateway($database);
        $controller = new AddonController($controller_gateway);
        $controller->processRequest(method: $method, colId: $colId, colPid: $colPid);
        break;
    default:
        http_response_code(response_code: 404);
        echo json_encode(["message" => "That resource is not available on this server."]);
        exit;
    }