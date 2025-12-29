<?php
class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    public $conn;

    public function __construct()
    {
        // Debug environment variables
        error_log("MYSQL_PUBLIC_URL: " . (getenv('MYSQL_PUBLIC_URL') ?: 'not set'));
        error_log("MYSQLHOST: " . (getenv('MYSQLHOST') ?: 'not set'));
        error_log("MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: 'not set'));
        
        // Check if running on Railway (production)
        if (getenv('MYSQL_PUBLIC_URL')) {
            // Parse Railway public MySQL URL
            $url = parse_url(getenv('MYSQL_PUBLIC_URL'));
            $this->host = $url['host'];
            $this->db_name = ltrim($url['path'], '/');
            $this->username = $url['user'];
            $this->password = $url['pass'];
            $this->port = $url['port'] ?? 3306;
            error_log("Using Railway Public MySQL: " . $this->host . ":" . $this->port);
        } elseif (getenv('MYSQLHOST') && getenv('MYSQLDATABASE')) {
            // Railway MySQL connection (fallback)
            $this->host = getenv('MYSQLHOST');
            $this->db_name = getenv('MYSQLDATABASE');
            $this->username = getenv('MYSQLUSER');
            $this->password = getenv('MYSQLPASSWORD');
            $this->port = getenv('MYSQLPORT') ?: 3306;
            error_log("Using Railway MySQL: " . $this->host);
        } elseif (php_sapi_name() === 'cli' || !isset($_SERVER['SERVER_NAME'])) {
            // CLI mode
            $this->host = "127.0.0.1";
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
            $this->port = 3306;
            error_log("Using CLI MySQL");
        } elseif (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] === '127.0.0.1'))) {
            // Localhost connection
            $this->host = "localhost";
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
            $this->port = 3306;
            error_log("Using localhost MySQL");
        } else {
            // Fallback connection
            $this->host = "localhost";
            $this->db_name = "medicine_dispenser";
            $this->username = "root";
            $this->password = "";
            $this->port = 3306;
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
                PDO::ATTR_TIMEOUT => 10,
            ];
            
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            error_log("Connecting with DSN: " . $dsn);
            error_log("Password length: " . strlen($this->password));
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

            // Set the MySQL session time zone to Asia/Manila
            $this->conn->exec("SET time_zone = '+08:00'");
            
            error_log("MySQL connection successful!");

        } catch (PDOException $exception) {
            error_log("PDO Exception: " . $exception->getMessage());
            echo "Connection error: " . $exception->getMessage();
            echo "<br>Host: " . $this->host;
            echo "<br>Port: " . $this->port;
            echo "<br>Database: " . $this->db_name;
            echo "<br>Username: " . $this->username;
            
            // Return null to prevent further errors
            return null;
        }

        return $this->conn;
    }
}
?>