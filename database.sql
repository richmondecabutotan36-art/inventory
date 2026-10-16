CREATE DATABASE IF NOT EXISTS inventory_system;
USE inventory_system;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) UNIQUE NOT NULL,
 password VARCHAR(255) NOT NULL,
 role VARCHAR(20) NOT NULL DEFAULT 'Admin'
);

CREATE TABLE categories (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE suppliers (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 contact VARCHAR(50) NOT NULL,
 email VARCHAR(150)
);

CREATE TABLE products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 product_name VARCHAR(150) NOT NULL,
 category_id INT NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 quantity INT NOT NULL DEFAULT 0,
 reorder_level INT NOT NULL DEFAULT 10,
 supplier_id INT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE RESTRICT,
 FOREIGN KEY(supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT
);

INSERT INTO users(username,password,role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC8m8q8Qm2M4bQfYxK6', 'Admin');

INSERT INTO categories(name) VALUES ('Office Supplies'),('Electronics'),('Food');
INSERT INTO suppliers(name,contact,email) VALUES
('ABC Supplies','09123456789','abc@example.com'),
('Tech Supplier','09987654321','tech@example.com');
INSERT INTO products(product_name,category_id,price,quantity,reorder_level,supplier_id) VALUES
('Ballpen',1,15.00,100,20,1),
('USB Flash Drive',2,350.00,15,10,2),
('Notebook',1,45.00,0,10,1);
