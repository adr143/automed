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
            // Check if running on production (PlanetScale)
            if (isset($_ENV['DATABASE_URL']) || getenv('DATABASE_URL')) {
                // PlanetScale connection
                $this->host = getenv('DB_HOST') ?: $_ENV['DB_HOST'];
                $this->db_name = getenv('DB_NAME') ?: $_ENV['DB_NAME'];
                $this->username = getenv('DB_USERNAME') ?: $_ENV['DB_USERNAME'];
                $this->password = getenv('DB_PASSWORD') ?: $_ENV['DB_PASSWORD'];
            } elseif ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '192.168.1.72') {
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
            // PlanetScale requires SSL and specific options
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