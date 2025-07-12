-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `test1`;
USE `test1`;

-- Create registration table
CREATE TABLE IF NOT EXISTS `registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `course` varchar(100) NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Optional: Insert sample data
INSERT INTO `registration` (`firstname`, `lastname`, `email`, `phone`, `gender`, `course`, `profile_picture`, `created_at`) VALUES
('John', 'Doe', 'john.doe@example.com', '1234567890', 'Male', 'Computer Science', 'uploads/profile_john.jpg', NOW()),
('Jane', 'Smith', 'jane.smith@example.com', '0987654321', 'Female', 'Data Science', 'uploads/profile_jane.jpg', NOW()),
('Alex', 'Johnson', 'alex.j@example.com', '5551234567', 'Other', 'Artificial Intelligence', 'uploads/profile_alex.jpg', NOW());

-- Create user with privileges (adjust password as needed)
CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY 'securepassword';
GRANT ALL PRIVILEGES ON `test1`.* TO 'admin'@'localhost';
FLUSH PRIVILEGES;