<?php
class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        // Check if running on Railway (has Railway environment variables)
        if (!empty(getenv('RAILWAY_ENVIRONMENT'))) {
            // Railway MySQL connection
            $this->host = getenv('MYSQLHOST') ?: getenv('RAILWAY_PRIVATE_DOMAIN');
            $this->db_name = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'railway';
            $this->username = getenv('MYSQLUSER') ?: 'root';
            $this->password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD');
        } else if (php_sapi_name() === 'cli' || !isset($_SERVER['SERVER_NAME'])) {
            // Default to localhost configuration for CLI
            $this->host = "127.0.0.1"; // Use 127.0.0.1 to force TCP connection
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
        } else {
            // Check if the server is running on localhost
            if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '192.168.1.72') {
                // Localhost connection
                $this->host = "localhost";
                $this->db_name = "medicine_dispenser";
                $this->username = "root";
                $this->password = "";
            } else {
                // Live server connection
                $this->host = "localhost";
                $this->db_name = "u607408406_automed";
                $this->username = "u607408406_automed";
                $this->password = "Automed2025@";
            }
        }
    }

    public function dbConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Set the MySQL session time zone to Asia/Manila
            $this->conn->exec("SET time_zone = '+08:00'");

        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>