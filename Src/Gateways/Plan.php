<?php
/**
 * This file contains Src/Gateways/Plan.php for project ESP-0002
 *
 * File Information
 * Project Designation: ESP-0002
 * Model Name: Src/Gateways
 * File Name: Plan.php
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
 * This class provided a gateway class to the Plan database table
 */
class Plan {

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
     * Method to return all plans in a given category
     *
     * @param string $colPid
     * @return array The returned plans
     */
    public function getAllForCategory(string $colPid): array {
        $sql = "SELECT * FROM tbl_plan WHERE colPid = :colPid ORDER BY colPid;";
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
        $sql = "SELECT * FROM tbl_plan WHERE colPid = :colPid AND colId = :colId";
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

    public function create(string $parent, array $data): string {
        $sql = "INSERT INTO tbl_plan (colId, colPid, colName, colMin, colMax, colTier1term, colTier1cost, colTier1sku, 
                    colTier2term, colTier2cost, colTier2sku) VALUES (:colId, :parent, :colName, :colMin, :colMax, 
                    :colTier1term, :colTier1cost, :colTier1sku, :colTier2term, :colTier2cost, :colTier2sku)";
        $stmt = $this->conn->prepare($sql);
        $new_id = $this->getNextPlanId($parent);
        $stmt->bindValue(param: ":colId", value: $new_id);
        $stmt->bindValue(param: ":colPid", value: $parent);
        $stmt->bindValue(param: ":colName", value: $data['colName']);
        $stmt->bindValue(param: ":colMin", value: $data['colMin']);
        $stmt->bindValue(param: ":colMax", value: $data['colMax']);
        $stmt->bindValue(param: ":colTier1term", value: $data['colTier1term']);
        $stmt->bindValue(param: ":colTier1cost", value: $data['colTier1cost']);
        $stmt->bindValue(param: ":colTier1sku", value: $data['colTier1sku']);
        $stmt->bindValue(param: ":colTier2term", value: $data['colTier2term']);
        $stmt->bindValue(param: ":colTier2cost", value: $data['colTier2cost']);
        $stmt->bindValue(param: ":colTier2sku", value: $data['colTier2sku']);
        $stmt->execute();
        return $parent . "-" . $new_id;
    }

    /**
     * Method to update a plan in the database
     *
     * @param string $colPid
     * @param string $colId
     * @param array $data The new plan data
     * @return int Count of rows updated
     */

    public function update(string $colPid, string $colId, array $data):int {
        $fields = [];
        if (!empty($data["colName"])) {
            $fields["colName"] = [
                $data["colName"],
                PDO::PARAM_STR
            ];
        }
        if (!empty($data["colMin"])) {
            $fields["colMin"] = [
                $data["colMin"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["colMax"])) {
            $fields["colMax"] = [
                $data["colMax"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["colTier1term"])) {
            $fields["colTier1term"] = [
                $data["colTier1term"],
                PDO::PARAM_STR
            ];
        }
        if (!empty($data["colTier1cost"])) {
            $fields["colTier1cost"] = [
                $data["colTier1cost"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["colTier1sku"])) {
            $fields["colTier1sku"] = [
                $data["colTier1sku"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["colTier2term"])) {
            $fields["colTier2term"] = [
                $data["colTier2term"],
                PDO::PARAM_STR
            ];
        }
        if (!empty($data["colTier2cost"])) {
            $fields["colTier2cost"] = [
                $data["colTier2cost"],
                PDO::PARAM_INT
            ];
        }
        if (!empty($data["colTier2sku"])) {
            $fields["colTier2sku"] = [
                $data["colTier2sku"],
                PDO::PARAM_INT
            ];
        }
        if (empty($fields)) {
            return 0;
        } else {
            $sets = array_map(function ($value) {
                return "$value = :$value";
            }, array_keys($fields));

            $sql = "UPDATE tbl_plan SET " . implode(separator: ", ", array: $sets) . " WHERE colId = :colId and colPid = :colPid";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(param: ":colId", value: $colId, type: PDO::PARAM_INT);
            $stmt->bindValue(param: ":colPid", value: $colPid, type: PDO::PARAM_INT);
            foreach ($fields as $colName => $values) {
                $stmt->bindValue(param: ":$colName", value: $values[0], type: $values[1]);
            }
            $stmt->execute();
            return $stmt->rowCount();
        }
    }

    /**
     * Method to delete a category from the database
     *
     * @param string $colPid The plan parent category
     * @param string $colId The plan to delete
     * @return int  Count of the rows deleted
     */

    public function delete(string $colPid, string $colId): int {
        $sql = "DELETE FROM tbl_plan WHERE colId = :colId AND colPid = :colPid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colId", value: $colId, type: PDO::PARAM_INT);
        $stmt->bindValue(param: ":colPid", value: $colPid, type: PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Private method to get the next available plan id given a category
     *
     * @param string $colPid
     * @return string The next plan id number
     */

    private function getNextPlanId(string $colPid):string {
        $sql = "SELECT count(*) FROM tbl_plan WHERE colPid = :colPid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(param: ":colPid", value: $colPid);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result + 1;
    }
}