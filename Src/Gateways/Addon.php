<?php
/**
 * This file contains Src/Gateways/Addon.php for project ESP-0002
 *
 * File Information
 * Project Name: ESP-0002
 * Model Name: Src/Gateways
 * File Name: Addon.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Last Modified: 08/30/2023
 * File Authored on: 03/24/2023
 * File Copyright: 03/2023
 */
namespace Gateways;

/**
 * Import needed classes
 */
use Database\Database;
use PDO;

/**
 * This class provides a gateway class to the Addon database table
 */
class Addon {

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
     * Method to return all addons in a given category
     *
     * @param string $colPid The category id
     * @return array The returned addons
     */
    public function getAllForCategory(string $colPid): array {
        $sql = "SELECT * FROM tbl_addons WHERE colPid = :colPid ORDER BY colId;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colPid", value: $colPid);
        $stmt->execute();
        return $stmt->fetchAll(mode: PDO::FETCH_ASSOC);
    }

    /**
     * Method to return a single plan given a category and plan id
     *
     * @param string $colPid
     * @param string $colId
     * @return array|false $data The returned plan or false is nothing returned
     */
    public function getSingle(string $colPid, string $colId): false|array
    {
        $sql = "SELECT * FROM tbl_addons WHERE colPid = :colPid AND colId = :colId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colPid", value: $colPid);
        $stmt->bindValue(param: ":colId", value: $colId);
        $stmt->execute();
        return $stmt->fetch(mode: PDO::FETCH_ASSOC);
    }

    /**
     * Method to add a plan to the database
     *
     * @param array $data The plan data
     * @return string The last inserted plan id
     * @noinspection SqlInsertValues
     */
    public function create(string $colPid, array $data): string {
        $sql = "INSERT INTO tbl_addons (colId, colPid, colTitle, colCost, colSku) VALUES 
               (:colId, :colPid, :colTitle, :colCost, :colSku)";
        $stmt = $this->conn->prepare($sql);
        $new_id = $this->getNextAddonId($colPid);
        $stmt->bindValue(param: ":colId", value: $new_id);
        $stmt->bindValue(param: ":colPid", value: $colPid);
        $stmt->bindValue(param: ":colTitle", value: $data['colTitle']);
        $stmt->bindValue(param: ":colCost", value: $data['colCost']);
        $stmt->bindValue(param: ":colSku", value: $data['colSku']);
        $stmt->execute();
        return $colPid . "-" . $new_id;
    }

    /**
     * Method to update an addon in the database
     *
     * @param string $colPid
     * @param string $colId
     * @param array $data The new addon data
     * @return int Count of rows updated
     */
    public function update(string $colPid, string $colId, array $data):int {
        $fields = [];
        if (!empty($data["colTitle"])) {
            $fields["colTitle"] = [
                $data["colTitle"],
                PDO::PARAM_STR
            ];
        }
        if (!empty($data["colCost"])) {
            $fields["colCost"] = [
                $data["colCost"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["colSku"])) {
            $fields["colSku"] = [
                $data["colSku"],
                PDO::PARAM_STR
            ];
        }
        if (empty($fields)) {
            return 0;
        } else {
            $sets = array_map(function ($value) {
                return "$value = :$value";
            }, array_keys($fields));
            $sql = "UPDATE tbl_addons SET " . implode(separator: ", ", array: $sets) . " WHERE colId = :colId and colPid = :colPid";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(param: ":colId", value: $colId, type: PDO::PARAM_INT);
            $stmt->bindValue(param: ":colPid", value: $colPid, type: PDO::PARAM_INT);
            foreach ($fields as $name => $values) {
                $stmt->bindValue(param: ":$name", value: $values[0], type: $values[1]);
            }
            $stmt->execute();
            return $stmt->rowCount();
        }
    }

    /**
     * Method to delete an addon from the database
     *
     * @param string $colPid
     * @param string $colId
     * @return int  Count of the rows deleted
     */

    public function delete(string $colPid, string $colId): int {
        $sql = "DELETE FROM tbl_addons WHERE colId = :colId AND colPid = :colPid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colId", value: $colId, type: PDO::PARAM_INT);
        $stmt->bindValue(param: ":colPid", value: $colPid, type: PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Private method to get the next available addon id given a category
     *
     * @param string $colPid
     * @return string The next addon id number
     */
    private function getNextAddonId(string $colPid):string {
        $sql = "SELECT count(*) FROM tbl_addons WHERE colPid = :colPid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colPid", value: $colPid);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result + 1;
    }
}