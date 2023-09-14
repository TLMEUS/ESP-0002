<?php
/**
 * This file contains Src/Gateways/Category.php for project ESP-0002
 *
 * File Information
 * Project Name: ESP-0002
 * Model Name: Src/Gateways
 * File Name: Category.php
 * File Version: 1.1.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Last Modified: 08/31/2023
 * File Authored on: 03/24/2023
 * File Copyright: 03/2023
 */
namespace Gateways;

/**
 * Import needed classes≤
 */
use Database\Database;
use PDO;

/**
 * This class provides a gateway class to the Category database table
 */
class Category {

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
     * Method to return all categories as an array
     *
     * @return array The returned categories
     */
    public function getAll(): array {
        $sql = "SELECT * FROM tbl_category ORDER BY colId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['colTsFlag'] = (bool)$row['colTsFlag'];
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Method to return a single category given an id
     *
     * @param string $colId
     * @return array $data The returned category or false is nothing returned
     */
    public function getSingle(string $colId): array {
        $sql = "SELECT * FROM tbl_category WHERE colId = :colId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colId", value: $colId);
        $stmt->execute();
        $data = $stmt->fetch(mode: PDO::FETCH_ASSOC);
        if ($data !== false) {
            $data['colTsFlag'] = (bool) $data['colTsFlag'];
        }
        return $data;
    }

    /**
     * Method add a new category to the database
     *
     * @param array $data JSON object of the data to insert
     * @return string The last inserted ID
     */
    public function create(array $data): string {
        $sql = "INSERT INTO tbl_category (colName, colTsFlag, colTsPercent) VALUES (:colName, :colTsFlag, :colTsPercent)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colName", value: $data["colName"]);
        $stmt->bindValue(param: ":colTsFlag", value: $data["colTsFlag"], type: PDO::PARAM_BOOL);
        $stmt->bindValue(param: ":colTsPercent", value: $data["colTsPercent"]);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    /**
     * Method to update a category in the database
     *
     * @param string $colId
     * @param array $data New data for the record
     * @return int Count of the rows updated
     */
    public function update(string $colId, array $data): int {
        $fields = [];
        if (!empty($data["colName"])) {
            $fields["colName"] = [
                $data["colName"],
                PDO::PARAM_STR
            ];
        }
        if (array_key_exists(key: "colTsFlag", array: $data)) {
            $fields["colTsFlag"] = [
                $data["colTsFlag"],
                $data["colTsFlag"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            ];
        }
        if (array_key_exists(key: "colTsPercent", array: $data)) {
            $fields["colTsPercent"] = [
                $data["colTsPercent"],
                $data["colTsPercent"] === null ? PDO::PARAM_NULL : PDO::PARAM_STR
            ];
        }
        if (empty($fields)) {
            return 0;
        } else {
            $sets = array_map(function ($value) {
                return "$value = :$value";
            }, array_keys($fields));
            $sql = "UPDATE tbl_category SET " . implode(separator: ", ", array: $sets) . " WHERE colId = :colId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(param: ":colId", value: $colId, type: PDO::PARAM_INT);
            foreach ($fields as $name => $values) {
                $stmt->bindValue(param: ":$name", value: $values[0], type: $values[1]);
            }
            $stmt->execute();
            return $stmt->rowCount();
        }
    }

    /**
     * Method to delete a category from the database
     *
     * @param string $colId
     * @return int  Count of the rows deleted
     */
    public function delete(string $colId): int {
        $sql = "DELETE FROM tbl_category WHERE colId = :colId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colId", value: $colId, type: PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}