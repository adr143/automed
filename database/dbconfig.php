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
        // Debug environment variables
        error_log("MYSQLHOST: " . (getenv('MYSQLHOST') ?: 'not set'));
        error_log("MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: 'not set'));
        error_log("MYSQLUSER: " . (getenv('MYSQLUSER') ?: 'not set'));
        
        // Check if running on Railway (production)
        if (getenv('MYSQLHOST') && getenv('MYSQLDATABASE')) {
            // Railway MySQL connection
            $this->host = getenv('MYSQLHOST');
            $this->db_name = getenv('MYSQLDATABASE');
            $this->username = getenv('MYSQLUSER');
            $this->password = getenv('MYSQLPASSWORD');
            error_log("Using Railway MySQL: " . $this->host);
        } elseif (php_sapi_name() === 'cli' || !isset($_SERVER['SERVER_NAME'])) {
            // CLI mode
            $this->host = "127.0.0.1";
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
            error_log("Using CLI MySQL");
        } elseif (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] === '127.0.0.1'))) {
            // Localhost connection
            $this->host = "localhost";
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
            error_log("Using localhost MySQL");
        } else {
            // Fallback connection
            $this->host = "localhost";
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
            error_log("Using fallback MySQL");
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
            
            // Add port if available
            $port = getenv('MYSQLPORT') ? ';port=' . getenv('MYSQLPORT') : '';
            $dsn = "mysql:host=" . $this->host . $port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            error_log("Connecting with DSN: " . $dsn);
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

            // Set the MySQL session time zone to Asia/Manila
            $this->conn->exec("SET time_zone = '+08:00'");

        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
            echo "<br>Host: " . $this->host;
            echo "<br>Database: " . $this->db_name;
            echo "<br>Username: " . $this->username;
            echo "<br>Port: " . (getenv('MYSQLPORT') ?: 'default');
        }

        return $this->conn;
    }
}
?>