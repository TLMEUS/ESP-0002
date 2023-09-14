<?php
/**
 * This file contains Src/Authorization/Auth.php for project ESP-0002
 *
 * File Information
 * Project Designation: ESP-0002
 * Model Name: Src/Authorization
 * File Name: Auth.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Last Modified: 08/30/2023
 * File Authored on: 03/15/2023
 * File Copyright: 03/2023
 */
namespace Authorization;

/**
 * Import need classes
 */
use Gateways\User as UserGateway;

/**
 * This class provides the authorization methods to the API
 */
class Auth {

    /**
     * @var int The user_id property
     */
    private int $userId;

    /**
     * @var UserGateway The user_gateway property
     */
    private UserGateway $user_gateway;

    /**
     * The class constructor
     *
     * @param UserGateway $user_gateway The User property
     */
    public function __construct(UserGateway $user_gateway) {
        $this->user_gateway = $user_gateway;
    }

    /**
     * Method to check if the supplied API key is valid.
     * A valid user id will be assigned to the $user_id property.
     *.
     * @return bool Returns true is valid API key, of FALSE on failure
     * @noinspection PhpUnused
     */
    public function authenticateAPIKey(): bool {
        if(empty($_SERVER["HTTP_X_API_KEY"])) {
            http_response_code(response_code: 400);
            echo json_encode(["message" => "Missing API key"]);
            return false;
        }
        $apiKey = $_SERVER["HTTP_X_API_KEY"];
        $user = $this->user_gateway->getByAPIKey(colApikey: $apiKey);
        $this->userId = $user["colId"];
        return true;
    }

    /**
     * The User ID getter
     *
     * @return int The authorized user ID
     * @noinspection PhpUnused
     */
    public function getUserID() : int {
        return $this->userId;
    }
}