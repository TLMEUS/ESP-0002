<?php
/**
 * This file contains Src/Database/Database.php for project ESP-0002
 *
 * File Information
 * Project Name: ESP-0002
 * Model Name: Src/Database
 * File Name: Database.php
 * File Version: 1.0.0
 * Author: Troy L Marker
 * Language: PHP 8.2
 *
 * File Last Modified: 08/30/2023
 * File Authored on: 03/24/2023
 * File Copyright: 03/2023
 */
namespace Database;

/**
 * Import required classes
 */
use PDO;

/**
 * This class provides database access to the API
 */
class Database {

    /**
     * @var PDO|null The PDO connection object
     */
    private ?PDO $conn = null;

    /**
     * @var string The database host server
     */
    private string $host;

    /**
     * @var string The database name
     */
    private string $name;

    /**
     * @var string The database username
     */
    private string $user;

    /**
     * @var string The database password
     */
    private string $password;

    /**
     * The class constructor
     *
     * @param string $host The database host server name
     * @param string $name The database name
     * @param string $user The database username
     * @param string $password The database password
     */
    public function __construct(string $host, string $name, string $user, string $password) {
        $this->password = $password;
        $this->user = $user;
        $this->name = $name;
        $this->host = $host;
    }

    /**
     * getConnection method
     *
     * This method returns the PDO connection
     *
     * @return PDO
     */
    public function getConnection() : PDO {
        if($this->conn === null) {
            $dsn = "mysql:host=$this->host;dbname=$this->name;charset=UTF8";
            $this->conn = new PDO(dsn: $dsn, username: $this->user, password: $this->password, options: [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);
        }
        return $this->conn;
    }
}