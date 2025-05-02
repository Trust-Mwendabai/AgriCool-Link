<?php
// Include database configuration
require_once 'config/database.php';

echo "<h1>AgriCool Link Database Debug</h1>";

// Check database connection
echo "<h2>Database Connection</h2>";
if ($conn) {
    echo "Connected to MySQL server successfully.<br>";
    echo "Server Info: " . mysqli_get_server_info($conn) . "<br>";
    
    // Check if database exists
    $result = mysqli_query($conn, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    if (mysqli_num_rows($result) > 0) {
        echo "Database '" . DB_NAME . "' exists.<br>";
    } else {
        echo "Database '" . DB_NAME . "' does not exist!<br>";
        
        // Try to create database again
        $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
        if (mysqli_query($conn, $sql)) {
            echo "Successfully created database.<br>";
        } else {
            echo "Failed to create database: " . mysqli_error($conn) . "<br>";
        }
    }
    
    // Select the database
    if (mysqli_select_db($conn, DB_NAME)) {
        echo "Database '" . DB_NAME . "' selected.<br>";
    } else {
        echo "Failed to select database: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "Failed to connect to MySQL server: " . mysqli_connect_error() . "<br>";
}

// Check tables
echo "<h2>Database Tables</h2>";
$tables = ['users', 'farmer_profiles', 'storage_provider_profiles', 'storage_units', 
           'categories', 'products', 'storage_bookings', 'orders', 'order_items'];

$missing_tables = [];
$table_rows = [];

foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "Table '$table' exists.<br>";
        
        // Count rows
        $count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM $table");
        $count = mysqli_fetch_assoc($count_result);
        $table_rows[$table] = $count['count'];
        echo "- Contains {$count['count']} rows.<br>";
    } else {
        echo "Table '$table' does not exist!<br>";
        $missing_tables[] = $table;
    }
}

// If any tables are missing, recreate them
if (!empty($missing_tables)) {
    echo "<h2>Recreating Missing Tables</h2>";
    
    echo "Running setup_tables.php script again...<br>";
    
    // Include setup tables script
    require_once 'config/setup_tables.php';
    
    // Check if tables were created
    echo "<h2>Verifying Tables After Recreating</h2>";
    foreach ($missing_tables as $table) {
        $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
        if (mysqli_num_rows($result) > 0) {
            echo "Table '$table' was successfully created.<br>";
        } else {
            echo "Table '$table' is still missing!<br>";
        }
    }
}

// Add seed data for testing if products table is empty
echo "<h2>Adding Seed Data for Testing</h2>";

if (isset($table_rows['products']) && $table_rows['products'] == 0) {
    echo "Products table is empty. Adding sample products...<br>";
    
    // First check if we have farmer profiles and categories
    $farmer_result = mysqli_query($conn, "SELECT id FROM farmer_profiles LIMIT 1");
    $category_result = mysqli_query($conn, "SELECT id FROM categories LIMIT 1");
    
    if (mysqli_num_rows($farmer_result) == 0) {
        echo "No farmer profiles found. Creating a test farmer profile...<br>";
        
        // Check if we have a user of type 'farmer'
        $user_result = mysqli_query($conn, "SELECT id FROM users WHERE user_type = 'farmer' LIMIT 1");
        
        if (mysqli_num_rows($user_result) == 0) {
            // Create a test farmer user
            echo "Creating a test farmer user...<br>";
            
            $hashed_password = password_hash('test123', PASSWORD_DEFAULT);
            $user_sql = "INSERT INTO users (username, email, password, first_name, last_name, phone, user_type, location) 
                         VALUES ('testfarmer', 'testfarmer@example.com', '$hashed_password', 'Test', 'Farmer', '1234567890', 'farmer', 'Lusaka, Zambia')";
            
            if (mysqli_query($conn, $user_sql)) {
                $user_id = mysqli_insert_id($conn);
                echo "Created test farmer user with ID: $user_id<br>";
                
                // Create farmer profile
                $profile_sql = "INSERT INTO farmer_profiles (user_id, farm_name, farm_size, farm_size_unit, farm_location) 
                               VALUES ($user_id, 'Test Farm', 5.0, 'hectares', 'Lusaka, Zambia')";
                
                if (mysqli_query($conn, $profile_sql)) {
                    $farmer_id = mysqli_insert_id($conn);
                    echo "Created test farmer profile with ID: $farmer_id<br>";
                } else {
                    echo "Failed to create farmer profile: " . mysqli_error($conn) . "<br>";
                }
            } else {
                echo "Failed to create test user: " . mysqli_error($conn) . "<br>";
            }
        } else {
            $user = mysqli_fetch_assoc($user_result);
            $user_id = $user['id'];
            
            $profile_sql = "INSERT INTO farmer_profiles (user_id, farm_name, farm_size, farm_size_unit, farm_location) 
                           VALUES ($user_id, 'Test Farm', 5.0, 'hectares', 'Lusaka, Zambia')";
            
            if (mysqli_query($conn, $profile_sql)) {
                $farmer_id = mysqli_insert_id($conn);
                echo "Created test farmer profile with ID: $farmer_id<br>";
            } else {
                echo "Failed to create farmer profile: " . mysqli_error($conn) . "<br>";
            }
        }
        
        $farmer_result = mysqli_query($conn, "SELECT id FROM farmer_profiles LIMIT 1");
    }
    
    // Now we should have a farmer profile
    if (mysqli_num_rows($farmer_result) > 0) {
        $farmer = mysqli_fetch_assoc($farmer_result);
        $farmer_id = $farmer['id'];
        
        // Get a category
        if (mysqli_num_rows($category_result) > 0) {
            $category = mysqli_fetch_assoc($category_result);
            $category_id = $category['id'];
            
            // Add sample products
            $sample_products = [
                ['Tomatoes', 'Fresh organic tomatoes', 25.00, 'kg', 10.0, '../images/tomatoes.jpg'],
                ['Cabbage', 'Organic cabbage', 15.00, 'head', 20.0, '../images/cabbage.jpg'],
                ['Carrots', 'Fresh carrots', 20.00, 'kg', 15.0, '../images/carrots.jpg'],
                ['Onions', 'Red and white onions', 18.00, 'kg', 25.0, '../images/onions.jpg']
            ];
            
            foreach ($sample_products as $product) {
                $product_sql = "INSERT INTO products (farmer_id, category_id, name, description, price, unit, quantity, image_url, status) 
                               VALUES ($farmer_id, $category_id, '{$product[0]}', '{$product[1]}', {$product[2]}, '{$product[3]}', {$product[4]}, '{$product[5]}', 'available')";
                
                if (mysqli_query($conn, $product_sql)) {
                    $product_id = mysqli_insert_id($conn);
                    echo "Added sample product '{$product[0]}' with ID: $product_id<br>";
                } else {
                    echo "Failed to add product '{$product[0]}': " . mysqli_error($conn) . "<br>";
                }
            }
        } else {
            echo "No categories found. Please run setup_tables.php again.<br>";
        }
    } else {
        echo "Failed to get or create a farmer profile.<br>";
    }
} else if (isset($table_rows['products'])) {
    echo "Products table already contains {$table_rows['products']} products. No need to add sample data.<br>";
} else {
    echo "Products table status could not be determined.<br>";
}

// Check and create placeholder image
echo "<h2>Creating Placeholder Image</h2>";
$placeholder_path = 'images/placeholder.jpg';
if (!file_exists($placeholder_path)) {
    echo "Placeholder image does not exist. Creating a simple placeholder...<br>";
    
    // Create a simple placeholder image
    $width = 400;
    $height = 300;
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bg_color = imagecolorallocate($image, 240, 240, 240);
    $text_color = imagecolorallocate($image, 100, 100, 100);
    
    // Fill background
    imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);
    
    // Add text
    $text = "Placeholder Image";
    $font_size = 5;
    $text_width = imagefontwidth($font_size) * strlen($text);
    $text_height = imagefontheight($font_size);
    
    // Center the text
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $x, $y, $text, $text_color);
    
    // Save the image
    imagejpeg($image, $placeholder_path);
    imagedestroy($image);
    
    echo "Placeholder image created at $placeholder_path<br>";
} else {
    echo "Placeholder image already exists at $placeholder_path<br>";
}

// Create 'uploads/products' directory if it doesn't exist
$upload_dir = 'uploads/products';
if (!file_exists($upload_dir)) {
    echo "Creating upload directory $upload_dir...<br>";
    if (mkdir($upload_dir, 0777, true)) {
        echo "Directory created successfully.<br>";
    } else {
        echo "Failed to create directory.<br>";
    }
} else {
    echo "Upload directory already exists.<br>";
}

echo "<h2>Database Debug Complete</h2>";
echo "<p>Try accessing the <a href='pages/marketplace.php'>marketplace</a> now.</p>";
?>
