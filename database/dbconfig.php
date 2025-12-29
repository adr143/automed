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
        // Check if running on Railway (production)
        if (getenv('MYSQLHOST')) {
            // Railway MySQL connection
            $this->host = getenv('MYSQLHOST');
            $this->db_name = getenv('MYSQLDATABASE');
            $this->username = getenv('MYSQLUSER');
            $this->password = getenv('MYSQLPASSWORD');
        } elseif (php_sapi_name() === 'cli' || !isset($_SERVER['SERVER_NAME'])) {
            // CLI mode
            $this->host = "127.0.0.1";
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
        } elseif (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] === '127.0.0.1'))) {
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
            echo "<br>Host: " . $this->host;
            echo "<br>Database: " . $this->db_name;
            echo "<br>Username: " . $this->username;
        }

        return $this->conn;
    }
}
?>