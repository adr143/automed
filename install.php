<?php
// Database installer for Railway
include_once 'database/dbconfig.php';

$database = new Database();
$conn = $database->dbConnection();

if ($conn) {
    echo "Connected to database successfully!<br>";
    
    // Create tables
    $sql = "
    CREATE TABLE IF NOT EXISTS `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `first_name` varchar(100) NOT NULL,
      `last_name` varchar(100) NOT NULL,
      `email` varchar(150) NOT NULL,
      `password` varchar(255) NOT NULL,
      `phone_number` varchar(20) DEFAULT NULL,
      `user_type` enum('admin','user') DEFAULT 'user',
      `account_status` enum('active','disabled') DEFAULT 'active',
      `tokencode` varchar(100) DEFAULT NULL,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `system_config` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `system_name` varchar(200) DEFAULT 'Smart Medicine Dispenser',
      `system_phone_number` varchar(20) DEFAULT NULL,
      `system_email` varchar(150) DEFAULT NULL,
      `system_logo` varchar(200) DEFAULT 'smart-medicine-logo.png',
      `system_favicon` varchar(200) DEFAULT NULL,
      `system_copy_right` varchar(200) DEFAULT 'COPYRIGHT © 2025 - SMART MEDICINE DISPENSER. ALL RIGHTS RESERVED.',
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `email_config` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `email` varchar(150) DEFAULT NULL,
      `password` varchar(150) DEFAULT NULL,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `google_recaptcha_api` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `site_key` varchar(150) DEFAULT NULL,
      `site_secret_key` varchar(150) DEFAULT NULL,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `logs` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) DEFAULT NULL,
      `activity` varchar(500) DEFAULT NULL,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    try {
        $conn->exec($sql);
        echo "Tables created successfully!<br>";
        
        // Insert default data
        $stmt = $conn->prepare("INSERT IGNORE INTO users (first_name, last_name, email, password, user_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Admin', 'User', 'admin@admin.com', '0192023a7bbd73250516f069df18b500', 'admin']);
        
        $stmt = $conn->prepare("INSERT IGNORE INTO system_config (system_name, system_email, system_logo) VALUES (?, ?, ?)");
        $stmt->execute(['Smart Medicine Dispenser', 'admin@medicinedispenser.com', 'smart-medicine-logo.png']);
        
        echo "Default data inserted!<br>";
        echo "<a href='index.php'>Go to App</a>";
        
    } catch (PDOException $e) {
        echo "Error creating tables: " . $e->getMessage();
    }
} else {
    echo "Failed to connect to database!";
}
?>