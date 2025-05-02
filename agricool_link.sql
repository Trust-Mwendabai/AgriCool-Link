-- AgriCool Link Database Schema
-- Created: May 2, 2025
-- For: Connecting Zambian farmers with reliable cold storage and direct market access

-- Create database
CREATE DATABASE IF NOT EXISTS agricool_link;
USE agricool_link;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    user_type ENUM('farmer', 'buyer', 'storage_provider', 'admin') NOT NULL,
    location VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active'
);

-- Farmer profiles table
CREATE TABLE IF NOT EXISTS farmer_profiles (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    farm_name VARCHAR(100),
    farm_size DECIMAL(10,2),
    farm_size_unit ENUM('hectares', 'acres'),
    farm_location VARCHAR(100),
    primary_produce VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Storage provider profiles table
CREATE TABLE IF NOT EXISTS storage_provider_profiles (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_name VARCHAR(100) NOT NULL,
    company_address VARCHAR(255) NOT NULL,
    company_phone VARCHAR(15) NOT NULL,
    company_email VARCHAR(100) NOT NULL,
    company_description TEXT,
    has_power_backup BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Storage units table
CREATE TABLE IF NOT EXISTS storage_units (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    provider_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    capacity DECIMAL(10,2) NOT NULL,
    capacity_unit ENUM('cubic_meters', 'tons') NOT NULL,
    temperature_range VARCHAR(50),
    location VARCHAR(100) NOT NULL,
    cost_per_day DECIMAL(10,2) NOT NULL,
    status ENUM('available', 'maintenance', 'offline') DEFAULT 'available',
    current_temperature DECIMAL(5,2),
    humidity_percentage DECIMAL(5,2),
    has_power BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (provider_id) REFERENCES storage_provider_profiles(id) ON DELETE CASCADE
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    farmer_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    harvest_date DATE,
    storage_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('available', 'sold', 'reserved') DEFAULT 'available',
    FOREIGN KEY (farmer_id) REFERENCES farmer_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (storage_id) REFERENCES storage_units(id) ON DELETE SET NULL
);

-- Storage bookings table
CREATE TABLE IF NOT EXISTS storage_bookings (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    farmer_id INT NOT NULL,
    storage_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    product_quantity DECIMAL(10,2),
    product_description TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    total_cost DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES farmer_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (storage_id) REFERENCES storage_units(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    buyer_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO categories (name, description) VALUES 
('Vegetables', 'Fresh vegetables from local farms'),
('Fruits', 'Seasonal and tropical fruits'),
('Grains', 'Maize, rice, and other grains'),
('Tubers', 'Potatoes, cassava, and other root crops'),
('Legumes', 'Beans, peas, and other legumes'),
('Herbs', 'Herbs and spices');

-- Insert demo user (farmer)
INSERT INTO users (username, email, password, first_name, last_name, phone, user_type, location) 
VALUES ('testfarmer', 'farmer@agricoollink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Farmer', '0971234567', 'farmer', 'Chipata, Zambia');

-- Create farmer profile for demo user
INSERT INTO farmer_profiles (user_id, farm_name, farm_size, farm_size_unit, farm_location, primary_produce) 
VALUES (1, 'Green Fields Farm', 5.5, 'hectares', 'Chipata, Eastern Province', 'Tomatoes, Cabbage, Maize');

-- Insert demo user (storage provider)
INSERT INTO users (username, email, password, first_name, last_name, phone, user_type, location) 
VALUES ('teststorage', 'storage@agricoollink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mary', 'Storage', '0969876543', 'storage_provider', 'Lusaka, Zambia');

-- Create storage provider profile for demo user
INSERT INTO storage_provider_profiles (user_id, company_name, company_address, company_phone, company_email, company_description, has_power_backup) 
VALUES (2, 'CoolStore Solutions', 'Plot 123, Industrial Area, Lusaka', '0969876543', 'info@coolstore.com', 'Providing reliable cold storage solutions with solar backup', TRUE);

-- Insert demo storage units
INSERT INTO storage_units (provider_id, name, description, capacity, capacity_unit, temperature_range, location, cost_per_day, current_temperature, humidity_percentage) 
VALUES 
(1, 'CoolUnit A', 'General purpose cold storage unit', 25.0, 'tons', '2-8°C', 'Lusaka, Industrial Area', 150.00, 4.5, 85.0),
(1, 'CoolUnit B', 'Deep freeze storage unit', 15.0, 'tons', '-18°C to -22°C', 'Lusaka, Industrial Area', 200.00, -20.0, 65.0),
(1, 'CoolUnit C', 'Fresh produce storage', 30.0, 'tons', '8-12°C', 'Lusaka, Industrial Area', 120.00, 10.0, 90.0);

-- Insert demo user (buyer)
INSERT INTO users (username, email, password, first_name, last_name, phone, user_type, location) 
VALUES ('testbuyer', 'buyer@agricoollink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Buyer', '0965432198', 'buyer', 'Lusaka, Zambia');

-- Insert sample products
INSERT INTO products (farmer_id, category_id, name, description, price, unit, quantity, image_url, status) 
VALUES 
(1, 1, 'Fresh Tomatoes', 'Organically grown tomatoes from Eastern Province', 25.00, 'kg', 100.0, '../images/tomatoes.jpg', 'available'),
(1, 1, 'Fresh Cabbage', 'Large green cabbage heads', 15.00, 'head', 50.0, '../images/cabbage.jpg', 'available'),
(1, 1, 'Carrots', 'Fresh orange carrots', 20.00, 'kg', 75.0, '../images/carrots.jpg', 'available'),
(1, 1, 'Onions', 'Red and white onions', 18.00, 'kg', 120.0, '../images/onions.jpg', 'available'),
(1, 2, 'Ripe Bananas', 'Sweet ripe bananas', 12.00, 'kg', 80.0, '../images/bananas.jpg', 'available'),
(1, 2, 'Fresh Oranges', 'Juicy oranges', 22.00, 'kg', 65.0, '../images/oranges.jpg', 'available'),
(1, 3, 'White Maize', 'High-quality maize grains', 180.00, '50kg', 10.0, '../images/maize.jpg', 'available'),
(1, 4, 'Sweet Potatoes', 'Orange-flesh sweet potatoes', 35.00, 'kg', 90.0, '../images/sweet-potatoes.jpg', 'available');
