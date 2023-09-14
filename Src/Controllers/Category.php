<?php
/**
 * This file contains Src/Controllers/Category.php for project EDP-0002
 *
 * File Information
 * Project Designation: ESP-0002
 * Model Name: Src/Controllers
 * File Name: Category.php
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
use Gateways\Category as CategoryGateway;

/**
 * Category
 *
 * This controller contains methods to retrieve information from the category table in the database
 *
 */
class Category {

    /**
     * @var CategoryGateway The Category property
     */
    private CategoryGateway $gateway;

    /**
     * The class constructor
     *
     * @param CategoryGateway $gateway The Category Gateway
     */
    public function __construct(CategoryGateway $gateway){
        $this->gateway = $gateway;
    }

    /**
     * processRequest Method
     *
     * This method processes the Category request
     *
     * @param string $method
     * @param string|null $colId
     * @return void
     */
    public function processRequest(string $method, ?string $colId): void {
        if ($colId === null) {
            if ($method == "GET") {
                echo json_encode(value: $this->gateway->getAll());
            } elseif ($method == "POST") {
                $data = (array)json_decode(json: file_get_contents(filename: "php://input"), associative: true);
                $errors = $this->getValidationErrors(data: $data);
                if (!empty($errors)) {
                    $this->categoryUnprocessableEntity(errors: $errors);
                    return;
                }
                $id = $this->gateway->create(data: $data);
                $this->categoryCreated(id: $id);
            } else {
                $this->categoryMethodNotAllowed(allowed_methods: "GET, POST");
            }
        } else {
            $category = $this->gateway->getSingle($colId);
            if ($category === false) {
                $this->categoryNotFound($colId);
                return;
            }
            switch ($method) {
                case "GET":
                    echo json_encode($category);
                    break;
                case "PATCH":
                    $data = (array)json_decode(json: file_get_contents("php://input"), associative: true);
                    $errors = $this->getValidationErrors(data: $data, is_new: false);
                    if (!empty($errors)) {
                        $this->categoryUnprocessableEntity(errors: $errors);
                        return;
                    }
                    $rows = $this->gateway->update(colId: $colId, data: $data);
                    http_response_code(response_code: 200);
                    echo json_encode(value: ["message" => "Category updated", "rows" => $rows]);
                    break;
                case "DELETE":
                    $rows = $this->gateway->delete(colId: $colId);
                    http_response_code(response_code: 200);
                    echo json_encode(value: ["message" => "Category deleted", "rows" => $rows]);
                    break;
                default:
                    $this->categoryMethodNotAllowed(allowed_methods: "GET, PATCH, DELETE");
                    break;
            }
        }
    }

    /**
     * categoryUnprocessableEntity Method
     *
     * This private method displays validation errors contained int the Category Data that caused the add function to fail.
     *
     * @param array $errors The list of errors
     * @return void
     */
    private function categoryUnprocessableEntity(array $errors): void
    {
        http_response_code(response_code: 422);
        echo json_encode(value: ['errors' => $errors]);
    }

    /**
     * categoryMethodNotAllowed Method
     *
     * This private method display the allowed Request Method when an incorrect Request is made.
     *
     * @param string $allowed_methods The list of allowed methods
     * @return void
     */
    private function categoryMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(response_code: 405);
        header(header: "Allow: $allowed_methods");
        echo json_encode(value: ['Allowed Methods' => $allowed_methods]);
    }

    /**
     * categoryNotFound Method
     *
     * This private method displays an error when a category is not found in the database.
     *
     * @param string $id The id of the missing category
     * @return void
     */
    private function categoryNotFound(string $id): void
    {
        http_response_code(response_code: 404);
        echo json_encode(value: ["message" => "Category with ID $id not found"]);
    }

    /**
     * categoryCreated Method
     *
     * This private method show a success message when the category is created.
     *
     * @param string $id The id of the created category.
     * @return void
     */
    private function categoryCreated(string $id): void
    {
        http_response_code(response_code: 201);
        echo json_encode(value: ["message" => "Category created", "id" => $id]);
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
    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];
        if ($is_new && empty($data['colName'])) {
            $errors [] = "Category is required.";
        }
        if (strlen($data['colName']) > 50) {
            $errors [] = "Category length is to long. Max of 50 characters.";
        }
        return $errors;
    }
}