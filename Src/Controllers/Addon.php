<?php
/**
 * This file contains Src/Controllers/Addon.php for project ESP-0002
 *
 * File Information
 * Project Name: ESP-0002
 * Model Name: Src/Controllers
 * File Name: Addon.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Last Modified: 08/31/2023
 * File Authored on: 03/24/2023
 * File Copyright: 03/2023
 */
namespace Controllers;

/**
 * Import needed classes
 */
use Gateways\Addon as AddonGateway;

/**
 * Plan
 *
 * This controller contains methods to retrieve information from the addon table in the database
 *
 */
class Addon {

    /**
     * @var AddonGateway The Addon property
     */
    private AddonGateway $gateway;

    /**
     * The class constructor
     *
     * @param AddonGateway $gateway The Addon Gateway
     */
    public function __construct(AddonGateway $gateway) {
        $this->gateway = $gateway;
    }

    /**
     * processRequest Method
     *
     * This method processes the Plan request
     *
     * @param string $method
     * @param string|null $colPid
     * @param string|null $colId
     * @return void
     */
    public function processRequest(string $method, ?string $colId, ?string $colPid): void {
        if ($colId === null) {
            switch ($method) {
                case "GET":
                    echo json_encode(value: $this->gateway->getAllForCategory($colPid));
                    break;
                case "POST":
                    $data = (array)json_decode(json: file_get_contents(filename: "php://input"), associative: true);
                    $errors = $this->getValidationErrors(data: $data);
                    if (!empty($error)) {
                        $this->addonUnprocessableEntity(errors: $errors);
                        return;
                    }
                    $id = $this->gateway->create(colPid: $colPid, data: $data);
                    $this->addonCreated(colId: $id);
                    break;
                default:
                    $this->addonMethodNotAllowed(allowed_methods: "GET, POST");
                    break;
            }
        } else {
            $addonSearch = $this->gateway->getSingle(colPid: $colPid, colId: $colId);
            if ($addonSearch === false) {
                $this->addonNotFound(colPid: $colPid, colId: $colId);
                return;
            }
            switch ($method) {
                case "GET":
                    echo json_encode(value: $addonSearch);
                    break;
                case "PATCH":
                    $data = (array)json_decode(json: file_get_contents(filename: "php://input"), associative: true);
                    $errors = $this->getValidationErrors(data: $data);
                    if (!empty($errors)) {
                        $this->addonUnprocessableEntity(errors: $errors);
                        return;
                    }
                    $rows = $this->gateway->update(colPid: $colPid, colId: $colId, data: $data);
                    http_response_code(response_code: 200);
                    echo json_encode(value: ["message" => "Addon updated", "rows" => $rows]);
                    break;
                case "DELETE":
                    $rows = $this->gateway->delete(colPid: $colPid, colId: $colId);
                    http_response_code(response_code: 200);
                    echo json_encode(value: ["message" => "Addon deleted.", "rows" => $rows]);
                    break;
                default:
                    $this->addonMethodNotAllowed(allowed_methods: "GET, PATCH, DELETE");
                    break;
            }
        }
    }

    /**
     * addonUnprocessableEntity Method
     *
     * This private method displays validation errors contained in the Addon Data that caused the add
     * function to fail.
     *
     * @param array $errors The list of errors
     * @return void
     */
    private function addonUnprocessableEntity(array $errors): void
    {
        http_response_code(response_code: 422);
        echo json_encode(value: ['errors' => $errors]);
    }

    /**
     * addonMethodNotAllowed Method
     *
     * This private method display the allowed Request Method when an incorrect Request is made.
     *
     * @param string $allowed_methods The list of allowed methods
     * @return void
     */
    private function addonMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(response_code: 405);
        header(header: "Allow: $allowed_methods");
        echo json_encode(value: ['Allowed Methods' => $allowed_methods]);
    }

    /**
     * addonNotFound Method
     *
     * This private method displays an error when an addon is not found in the database.
     *
     * @param string $colPid
     * @param string $colId
     * @return void
     */
    private function addonNotFound(string $colPid, string $colId): void
    {
        http_response_code(response_code: 404);
        echo json_encode(value: ["message" => "Addon in category $colPid with ID $colId not found"]);
    }

    /**
     * planCreated Method
     *
     * This private method show a success message when the plan is created.
     *
     * @param string $colId
     * @return void
     */
    private function addonCreated(string $colId): void
    {
        http_response_code(response_code: 201);
        echo json_encode(value: ["message" => "Addon created", "Category-Addon" => $colId]);
    }

    /**
     * getValidationErrors Method
     *
     * This private method checks the data array for errors
     *
     * @param array $data The post data array
     * @return array Array containing all validation errors
     */
    private function getValidationErrors(array $data): array {
        $errors = [];
        if (strlen($data['colTitle']) > 100) {
            $errors [] = "Title length is to long. Max of 100 characters.";
        }
        if (!empty($data['colCost']) && !is_double($data['colCost']) ) {
            $errors[] = "Addon cost is not a valid value.";
        }
        if (!empty($data['colSku']) && !is_int($data['colSku'])) {
            $errors[] = 'Addon SKU is nat a valid value.';
        }
        return $errors;
    }
}