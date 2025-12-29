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
        // Load environment variables from .env file if exists
        $this->loadEnv();

        // Check if running on Railway (has Railway environment variables)
        if (!empty(getenv('RAILWAY_ENVIRONMENT'))) {
            // Railway MySQL connection
            $this->host = getenv('MYSQLHOST') ?: getenv('RAILWAY_PRIVATE_DOMAIN');
            $this->db_name = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'railway';
            $this->username = getenv('MYSQLUSER') ?: 'root';
            $this->password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD');
        } 
        // Check for environment variables (InfinityFree, shared hosting, etc.)
        else if (!empty(getenv('DB_HOST'))) {
            $this->host = getenv('DB_HOST');
            $this->db_name = getenv('DB_NAME');
            $this->username = getenv('DB_USER');
            $this->password = getenv('DB_PASS');
        }
        // CLI mode
        else if (php_sapi_name() === 'cli' || !isset($_SERVER['SERVER_NAME'])) {
            $this->host = "127.0.0.1";
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
        } 
        // Local development
        else if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '192.168.1.72') {
            $this->host = "localhost";
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
        } 
        // Production/shared hosting fallback (InfinityFree)
        else {
            $this->host = "localhost";
            $this->db_name = getenv('DB_NAME') ?: "u607408406_automed";
            $this->username = getenv('DB_USER') ?: "u607408406_automed";
            $this->password = getenv('DB_PASS') ?: "Automed2025@";
        }
    }

    private function loadEnv()
    {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    [$key, $value] = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    if (!getenv($key)) {
                        putenv("$key=$value");
                    }
                }
            }
        }
    }

    public function dbConnection()
    {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Set the MySQL session time zone to Asia/Manila
            $this->conn->exec("SET time_zone = '+08:00'");

        } catch (PDOException $exception) {
            error_log("Database Connection Error: " . $exception->getMessage());
            die("Connection error: Unable to connect to database. Please verify your database configuration in .env file.");
        }

        return $this->conn;
    }
}
?>
