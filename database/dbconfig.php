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
        // Check if the script is running in CLI mode
        if (php_sapi_name() === 'cli' || !isset($_SERVER['SERVER_NAME'])) {
            // Default to localhost configuration for CLI
            $this->host = "127.0.0.1";
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
        } else {
            // Check if running on Railway (production)
            if (getenv('MYSQLDATABASE') || getenv('MYSQLHOST')) {
                // Railway MySQL connection
                $this->host = getenv('MYSQLHOST');
                $this->db_name = getenv('MYSQLDATABASE');
                $this->username = getenv('MYSQLUSER');
                $this->password = getenv('MYSQLPASSWORD');
            } elseif ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '192.168.1.72') {
                // Localhost connection
                $this->host = "localhost";
                $this->db_name = "medicine_dispenser";
                $this->username = "root";
                $this->password = "";
            } else {
                // Fallback connection
                $this->host = "localhost";
                $this->db_name = "medicine_dispenser";
                $this->username = "root";
                $this->password = "";
            }
        }
    }

    public function dbConnection()
    {
        $this->conn = null;
        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ];
            
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", $this->username, $this->password, $options);

            // Set the MySQL session time zone to Asia/Manila
            $this->conn->exec("SET time_zone = '+08:00'");

        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>