-- Medicine Dispenser Database
-- Database: `medicine_dispenser`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `user_type` enum('admin','user') DEFAULT 'user',
  `account_status` enum('active','disabled') DEFAULT 'active',
  `tokencode` varchar(100) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (email: admin@admin.com, password: admin123)
INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`, `user_type`) VALUES
('Admin', 'User', 'admin@admin.com', '0192023a7bbd73250516f069df18b500', 'admin');

-- --------------------------------------------------------

-- Table structure for table `medicines`
CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `medicine_name` varchar(200) NOT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `schedules`
CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `schedule_time` time NOT NULL,
  `frequency` enum('daily','weekly','monthly') DEFAULT 'daily',
  `days_of_week` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `dispensing_logs`
CREATE TABLE `dispensing_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `dispensed_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `quantity_dispensed` int(11) DEFAULT 1,
  `status` enum('dispensed','missed','manual') DEFAULT 'dispensed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `logs`
CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity` varchar(500) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `system_config`
CREATE TABLE `system_config` (
  `id` int(11) NOT NULL,
  `system_name` varchar(200) DEFAULT 'Smart Medicine Dispenser',
  `system_phone_number` varchar(20) DEFAULT NULL,
  `system_email` varchar(150) DEFAULT NULL,
  `system_logo` varchar(200) DEFAULT 'smart-medicine-logo.png',
  `system_favicon` varchar(200) DEFAULT NULL,
  `system_copy_right` varchar(200) DEFAULT 'COPYRIGHT © 2025 - SMART MEDICINE DISPENSER. ALL RIGHTS RESERVED.',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default system config
INSERT INTO `system_config` (`system_name`, `system_phone_number`, `system_email`, `system_logo`, `system_favicon`) VALUES
('Smart Medicine Dispenser', NULL, 'admin@medicinedispenser.com', 'smart-medicine-logo.png', NULL);

-- --------------------------------------------------------

-- Table structure for table `email_config`
CREATE TABLE `email_config` (
  `id` int(11) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `google_recaptcha_api`
CREATE TABLE `google_recaptcha_api` (
  `id` int(11) NOT NULL,
  `site_key` varchar(150) DEFAULT NULL,
  `site_secret_key` varchar(150) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Indexes and Auto Increment

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `medicine_id` (`medicine_id`);

ALTER TABLE `dispensing_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `medicine_id` (`medicine_id`),
  ADD KEY `schedule_id` (`schedule_id`);

ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `email_config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `google_recaptcha_api`
  ADD PRIMARY KEY (`id`);

-- Auto Increment
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `medicines` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `schedules` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `dispensing_logs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `logs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `system_config` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `email_config` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `google_recaptcha_api` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Foreign Keys
ALTER TABLE `medicines`
  ADD CONSTRAINT `medicines_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE;

ALTER TABLE `dispensing_logs`
  ADD CONSTRAINT `dispensing_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispensing_logs_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispensing_logs_ibfk_3` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE SET NULL;

COMMIT;