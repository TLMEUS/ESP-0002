<?php
/**
 * This file contains Src/Controllers/Plan.php for project ESP-0002
 *
 * File Information
 * Project Designation: ESP-0002
 * Model Name: Src/Controllers
 * File Name: Plan.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Last Modified: 08/30/2023
 * File Authored on: 03/24/2023
 * File Copyright: 03/2023
 */
namespace Controllers;

/**
 * Import needed classes
 */
use Gateways\Plan as PlanGateway;

/**
 * Plan
 *
 * This controller contains methods to retrieve information from the plan table in the database
 *
 */
class Plan {

    /**
     * @var PlanGateway The Plan property
     */
    private PlanGateway $gateway;

    /**
     * The class constructor
     *
     * @param PlanGateway $gateway The Plan Gateway
     */
    public function __construct(PlanGateway $gateway) {
        $this->gateway = $gateway;
    }

    /**
     * processRequest Method
     *
     * This method processes the Plan request
     *
     * @param string $method
     * @param string|null $colId
     * @param string|null $colPid
     * @return void
     */
    public function processRequest(string $method, ?string $colPid, ?string $colId): void {
        if ($colId == null) {
            switch ($method) {
                case "GET":
                    echo json_encode(value: $this->gateway->getAllForCategory(colPid: $colPid));
                    break;
                case "POST":
                    $data = (array)json_decode(json: file_get_contents(filename: "php://input"), associative: true);
                    $errors = $this->getValidationErrors(data: $data);
                    if (!empty($error)) {
                        $this->planUnprocessableEntity(errors: $errors);
                        return;
                    }
                    $id = $this->gateway->create(parent: $colPid, data: $data);
                    $this->planCreated(colId: $id);
                    break;
                default:
                    $this->planMethodNotAllowed(allowed_methods: "GET, POST");
                    break;
            }
        } else {
            $plansearch = $this->gateway->getSingle(colPid: $colPid, colId: $colId);
            if ($plansearch === false) {
                $this->planNotFound(colId: $colId, colPid: $colPid);
                return;
            }
            switch ($method) {
                case "GET":
                    echo json_encode(value: $plansearch);
                    break;
                case "PATCH":
                    $data = (array)json_decode(json: file_get_contents(filename: "php://input"), associative: true);
                    $errors = $this->getValidationErrors(data: $data, is_new: false);
                    if (!empty($errors)) {
                        $this->planUnprocessableEntity(errors: $errors);
                        return;
                    }
                    $rows = $this->gateway->update(colPid: $colPid, colId: $colId, data: $data);
                    http_response_code(response_code: 200);
                    echo json_encode(value: ["message" => "Plan updated", "rows" => $rows]);
                    break;
                case "DELETE":
                    $rows = $this->gateway->delete(colPid: $colPid, colId: $colId);
                    http_response_code(response_code: 200);
                    echo json_encode(value: ["message" => "Plan deleted.", "rows" => $rows]);
                    break;
                default:
                    $this->planMethodNotAllowed(allowed_methods: "GET, PATCH, DELETE");
                    break;
            }
        }
    }

    /**
     * planUnprocessableEntity Method
     *
     * This private method displays validation errors contained in the Plan Data that caused the add function to fail.
     *
     * @param array $errors The list of errors
     * @return void
     */
    private function planUnprocessableEntity(array $errors): void
    {
        http_response_code(response_code: 422);
        echo json_encode(value: ['errors' => $errors]);
    }

    /**
     * planMethodNotAllowed Method
     *
     * This private method display the allowed Request Method when an incorrect Request is made.
     *
     * @param string $allowed_methods The list of allowed methods
     * @return void
     */
    private function planMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(response_code: 405);
        header(header: "Allow: $allowed_methods");
        echo json_encode(value: ['Allowed Methods' => $allowed_methods]);
    }

    /**
     * planNotFound Method
     *
     * This private method displays an error when a plan is not found in the database.
     *
     * @param string $colId
     * @param string $colPid
     * @return void
     */
    private function planNotFound(string $colId, string $colPid): void
    {
        http_response_code(response_code: 404);
        echo json_encode(value: ["message" => "Plan in category $colId with ID $colPid not found"]);
    }

    /**
     * planCreated Method
     *
     * This private method show a success message when the plan is created.
     *
     * @param string $colId
     * @return void
     */
    private function planCreated(string $colId): void
    {
        http_response_code(response_code: 201);
        echo json_encode(value: ["message" => "Plan created", "Category-Plan" => $colId]);
    }

    /**
     * getValidationErrors Method
     *
     * This private method checks the data array for errors
     *
     * @param array $data The post data array
     * @param bool $is_new Flag indicating if the category is new
     * @return array Array containing all validation errors
     */
    private function getValidationErrors(array $data, bool $is_new = true): array {
        $errors = [];
        if (strlen(string: $data['colName']) > 100) {
            $errors [] = "Plan length is to long. Max of 100 characters.";
        }
        if (!empty($data['colMin']) && !is_double(value: $data['colMin']) ) {
            $errors[] = "Minimum cost is not a valid value.";
        }
        if (!empty($data['colMax']) && !is_double(value: 'colMax')) {
            $errors[] = 'Maximum cost is not a valid value.';
        }
        if($is_new && empty($data['colTier1term'])) {
            $errors[] = 'Tier 1 term is required.';
        }
        if (!empty($data['colTier1term']) && strlen(string: $data['colTier1term'] > 10)) {
            $errors[] = 'Tier 1 Term is too long.';
        }
        if (!empty($data['colTier1cost']) && !is_double(value: $data['colTier1cost'])) {
            $errors[] ='Tier 1 cost is not a valid value';
        }
        if (!empty($data['colTier1sku']) && !is_int(value: $data['colTier1sku'])) {
            $errors[] = 'Tier 1 sku is nat a valid value.';
        }
        if (!empty($data['colTier2term'])) {
            if (strlen(string: $data['colTier2term'] > 10)) {
                $errors[] = 'Tier 2 Term is too long.';
            }
            if (!empty($data['colTier2cost']) && !is_double(value: $data['colTier2cost'])) {
                $errors[] ='Tier 2 cost is not a valid value';
            }
            if (!empty($data['colTier2sku']) && !is_int(value: $data['colTier2sku'])) {
                $errors[] = 'Tier 2 sku is nat a valid value.';
            }
        }
        return $errors;
    }
}