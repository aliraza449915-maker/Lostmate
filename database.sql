CREATE DATABASE IF NOT EXISTS lostmate_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lostmate_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  address VARCHAR(255),
  phone VARCHAR(50),
  avatar VARCHAR(255) DEFAULT NULL,
  is_admin TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  item_name VARCHAR(200) NOT NULL,
  description TEXT,
  image VARCHAR(255) DEFAULT NULL,
  type ENUM('lost','found') NOT NULL,
  status ENUM('active','found','returned') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Default admin account
-- email: ali.raza449915@gmail.com
-- password: admin123
INSERT INTO users (name, email, password, address, phone, is_admin)
VALUES ('Admin', 'ali.raza449915@gmail.com', 'admin123', 'Admin Address', '0000000000', 1);
