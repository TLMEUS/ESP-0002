<?php
/**
 * This file contains Src/Gateways/User.php for project ESP-0002
 *
 * File Information
 * Project Name: ESP-0002
 * Model Name: Src/Gateways
 * File Name: User.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Last Modified: 08/30/2023
 * File Authored on: 03/24/2023
 * File Copyright: 03/2023
 */
namespace Gateways;

use Database\Database;
use PDO;

/**
 * This class provides a gateway to the user table in the database
 */
class User {

    /**
     * @var PDO The database connection object
     */
    private PDO $conn;

    /**
     * The class constructor
     *
     * @param Database $database The database object
     */
    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    /**
     * Method to get a user info with a supplied API Key
     *
     * @param string $colApikey
     * @return array The user info is user exists, false otherwise
     */
    public function getByAPIKey(string $colApikey): array {
        $sql = 'SELECT * FROM tbl_apikeys WHERE colApikey = :colApikey';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colApikey", value: $colApikey);
        $stmt->execute();
        return $stmt->fetch(mode: PDO::FETCH_ASSOC);
    }

    /**
     * Method to get user info with a supplied username
     *
     * @param string $colUsername
     * @return array The user info is user exists, false otherwise
     */
    public function getByUsername(string $colUsername): array {
        $sql = "SELECT * FROM tbl_apikeys WHERE colUsername = :colUsername";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colUsername", value: $colUsername);
        $stmt->execute();
        return $stmt->fetch(mode: PDO::FETCH_ASSOC);
    }

    /**
     * Method to get user info with a supplied user ID
     * @param int $colId
     * @return array The user info is user exists, false otherwise
     */
    public function getByID(int $colId): array {
        $sql = "SELECT * FROM tbl_apikeys WHERE colId = :colId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colId", value: $colId, type: PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(mode: PDO::FETCH_ASSOC);
    }
}