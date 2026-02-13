<?php
// app/config/Database.php

class Database {
    private $host;
    private $user;
    private $password;
    private $db_name;
    private $port;
    private $conn;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->user = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        $this->db_name = $_ENV['DB_NAME'] ?? 'inventory_supplements';
        $this->port = $_ENV['DB_PORT'] ?? 3306;
    }

    public function connect() {
        try {
            $this->conn = new mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->db_name,
                $this->port
            );

            if ($this->conn->connect_error) {
                throw new Exception("Error de conexión: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
            return $this->conn;
        } catch (Exception $e) {
            die("Conexión a BD fallida: " . $e->getMessage());
        }
    }

    public function getConnection() {
        if (!$this->conn) {
            $this->connect();
        }
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
