<?php
// Debug Railway environment variables
echo "Available environment variables:\n";
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'MYSQL') !== false || strpos($key, 'RAILWAY') !== false) {
        echo "$key = $value\n";
    }
}

// Try different connection approaches
$configs = [
    ['host' => getenv('RAILWAY_PRIVATE_DOMAIN'), 'name' => 'RAILWAY_PRIVATE_DOMAIN'],
    ['host' => getenv('MYSQLHOST'), 'name' => 'MYSQLHOST'],
    ['host' => getenv('DATABASE_HOST'), 'name' => 'DATABASE_HOST']
];

$database = getenv('MYSQLDATABASE') ?: 'railway';
$username = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQL_ROOT_PASSWORD') ?: getenv('MYSQLPASSWORD');
$port = getenv('MYSQLPORT') ?: '3306';

foreach ($configs as $config) {
    if ($config['host']) {
        echo "\nTrying {$config['name']}: {$config['host']}\n";
        try {
            $pdo = new PDO("mysql:host={$config['host']};port=$port;charset=utf8mb4", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ]);
            
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
            $pdo = new PDO("mysql:host={$config['host']};port=$port;dbname=$database;charset=utf8mb4", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ]);
            
            $sql = file_get_contents('database/medicine_dispenser.sql');
            $statements = array_filter(array_map('trim', explode(';', preg_replace('/--.*$/m', '', $sql))));
            
            foreach ($statements as $statement) {
                if (!empty($statement)) $pdo->exec($statement);
            }
            
            file_put_contents('.installed', date('Y-m-d H:i:s'));
            echo json_encode(['status' => 'success', 'host' => $config['host']]);
            exit;
            
        } catch (Exception $e) {
            echo "Failed with {$config['name']}: " . $e->getMessage() . "\n";
        }
    }
}

echo json_encode(['status' => 'error', 'message' => 'No valid database connection found']);
?>