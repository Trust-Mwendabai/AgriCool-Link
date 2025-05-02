<?php
// Include database configuration
require_once 'database.php';

// SQL to create users table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
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
)";

// SQL to create farmer_profiles table
$sql_farmer_profiles = "CREATE TABLE IF NOT EXISTS farmer_profiles (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    farm_name VARCHAR(100),
    farm_size DECIMAL(10,2),
    farm_size_unit ENUM('hectares', 'acres'),
    farm_location VARCHAR(100),
    primary_produce VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// SQL to create storage_provider_profiles table
$sql_storage_provider_profiles = "CREATE TABLE IF NOT EXISTS storage_provider_profiles (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_name VARCHAR(100) NOT NULL,
    company_address VARCHAR(255) NOT NULL,
    company_phone VARCHAR(15) NOT NULL,
    company_email VARCHAR(100) NOT NULL,
    company_description TEXT,
    has_power_backup BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

// SQL to create storage_units table
$sql_storage_units = "CREATE TABLE IF NOT EXISTS storage_units (
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
)";

// SQL to create categories table
$sql_categories = "CREATE TABLE IF NOT EXISTS categories (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
)";

// SQL to create products table
$sql_products = "CREATE TABLE IF NOT EXISTS products (
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
)";

// SQL to create storage_bookings table
$sql_storage_bookings = "CREATE TABLE IF NOT EXISTS storage_bookings (
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
)";

// SQL to create orders table
$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    buyer_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
)";

// SQL to create order_items table
$sql_order_items = "CREATE TABLE IF NOT EXISTS order_items (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

// Execute each query and report status
$tables = [
    'users' => $sql_users,
    'farmer_profiles' => $sql_farmer_profiles,
    'storage_provider_profiles' => $sql_storage_provider_profiles,
    'storage_units' => $sql_storage_units,
    'categories' => $sql_categories,
    'products' => $sql_products,
    'storage_bookings' => $sql_storage_bookings,
    'orders' => $sql_orders,
    'order_items' => $sql_order_items
];

foreach ($tables as $table => $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "Table $table created successfully.<br>";
    } else {
        echo "ERROR: Could not create table $table. " . mysqli_error($conn) . "<br>";
    }
}

// Insert default categories
$default_categories = [
    ['Vegetables', 'Fresh vegetables from local farms'],
    ['Fruits', 'Seasonal and tropical fruits'],
    ['Grains', 'Maize, rice, and other grains'],
    ['Tubers', 'Potatoes, cassava, and other root crops'],
    ['Legumes', 'Beans, peas, and other legumes'],
    ['Herbs', 'Herbs and spices']
];

foreach ($default_categories as $category) {
    $name = $category[0];
    $description = $category[1];
    
    // Check if category already exists
    $check_query = "SELECT id FROM categories WHERE name = '$name'";
    $result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($result) == 0) {
        $insert_sql = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
        if (mysqli_query($conn, $insert_sql)) {
            echo "Default category '$name' added successfully.<br>";
        } else {
            echo "ERROR: Could not add default category '$name'. " . mysqli_error($conn) . "<br>";
        }
    }
}

echo "Database setup completed.";
?>
