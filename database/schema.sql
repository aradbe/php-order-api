CREATE DATABASE IF NOT EXISTS php_order_api;
USE php_order_api;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0
);

CREATE TABLE orders(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    total_price DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE order_items(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY(order_id) REFERENCES orders(id),
    FOREIGN KEY(product_id) REFERENCES products(id)
);

INSERT INTO products (name, price, stock)
VALUES
    ('Keyboard', 99.90, 10),
    ('Mouse',49.90,20),
    ('Monitor', 799.00, 5);