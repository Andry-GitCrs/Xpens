<?php
require_once 'db.php';

$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) UNIQUE NOT NULL,
  profile_picture VARCHAR(255) DEFAULT 'default_profile.png',
  email VARCHAR(255) UNIQUE NOT NULL,
  password TEXT NOT NULL,
  stat BOOLEAN DEFAULT TRUE
)");

$pdo->exec("
CREATE TABLE IF NOT EXISTS purchases (
  id_purchase INT AUTO_INCREMENT PRIMARY KEY,
  number INT NOT NULL,
  total_price DECIMAL(10, 2) DEFAULT 0.00,
  stat BOOLEAN DEFAULT TRUE,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES users(id_user)
)");

$pdo->exec("
CREATE TABLE IF NOT EXISTS products (
  id_product INT AUTO_INCREMENT PRIMARY KEY,
  product_name VARCHAR(255) UNIQUE NOT NULL,
  price DECIMAL(10, 2) DEFAULT 0.00,
  stat BOOLEAN DEFAULT TRUE,
  created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  purchase_id INT,
  FOREIGN KEY (purchase_id) REFERENCES purchases(id_purchase)
)");
