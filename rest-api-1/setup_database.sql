CREATE DATABASE IF NOT EXISTS `rest_api` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'api_user'@'localhost' IDENTIFIED BY 'secure_password_123';
GRANT ALL PRIVILEGES ON `rest_api`.* TO 'api_user'@'localhost';
FLUSH PRIVILEGES;

USE `rest_api`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
    ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`name`, `email`, `password`) VALUES
('John Doe', 'john.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane Smith', 'jane.smith@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Bob Johnson', 'bob.johnson@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

CREATE INDEX `idx_email` ON `users` (`email`);
CREATE INDEX `idx_name` ON `users` (`name`);

SELECT * FROM `users` LIMIT 5;

-- Run this SQL in phpMyAdmin or any MySQL client
UPDATE users 
SET password = '$2y$10$NeWSeCuReHaShGoEsHeRE.12345678901234567890123456789012'
WHERE email = 'john.doe@example.com'