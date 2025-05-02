<?php
// Database connection
require_once 'config/database.php';

// Check if connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// List of products to add (name, category_id, price, unit, quantity, description, image_url)
$products = [
    // Vegetables (Category ID: 1)
    ['Green Peppers', 1, 18.50, 'kg', 85.0, 'Fresh green bell peppers from Eastern Province', '../images/green_pepper.jpg'],
    ['Spinach', 1, 12.00, 'bunch', 120.0, 'Organic leafy spinach', '../images/spinach.jpg'],
    ['Lettuce', 1, 15.00, 'head', 65.0, 'Crisp green lettuce', '../images/lettuce.jpg'],
    ['Cucumber', 1, 14.00, 'kg', 90.0, 'Fresh green cucumbers', '../images/cucumber.jpg'],
    ['Broccoli', 1, 25.00, 'kg', 45.0, 'Nutritious green broccoli', '../images/broccoli.jpg'],

    // Fruits (Category ID: 2)
    ['Mangoes', 2, 20.00, 'kg', 75.0, 'Sweet juicy mangoes', '../images/mangoes.jpg'],
    ['Pineapples', 2, 30.00, 'piece', 40.0, 'Ripe sweet pineapples', '../images/pineapples.jpg'],
    ['Watermelons', 2, 35.00, 'piece', 30.0, 'Large juicy watermelons', '../images/watermelons.jpg'],
    ['Avocados', 2, 28.00, 'kg', 60.0, 'Creamy ripe avocados', '../images/avocados.jpg'],
    ['Papaya', 2, 18.00, 'piece', 50.0, 'Sweet tropical papaya', '../images/papaya.jpg'],

    // Grains (Category ID: 3)
    ['Rice', 3, 200.00, '50kg', 15.0, 'High-quality local rice', '../images/rice.jpg'],
    ['Sorghum', 3, 180.00, '50kg', 12.0, 'Locally grown sorghum', '../images/sorghum.jpg'],
    ['Millet', 3, 175.00, '50kg', 10.0, 'Nutritious millet grains', '../images/millet.jpg'],
    ['Wheat', 3, 220.00, '50kg', 8.0, 'Premium wheat grains', '../images/wheat.jpg'],
    ['Soybeans', 3, 250.00, '50kg', 14.0, 'Protein-rich soybeans', '../images/soybeans.jpg'],

    // Tubers (Category ID: 4)
    ['Cassava', 4, 18.00, 'kg', 100.0, 'Fresh cassava roots', '../images/cassava.jpg'],
    ['Irish Potatoes', 4, 22.00, 'kg', 85.0, 'Irish potatoes', '../images/potatoes.jpg'],
    ['Yams', 4, 25.00, 'kg', 70.0, 'Nutritious yams', '../images/yams.jpg'],
    ['Sweet Potatoes', 4, 20.00, 'kg', 90.0, 'Sweet orange-flesh sweet potatoes', '../images/sweet_potatoes.jpeg'],
    ['Pumpkins', 4, 18.00, 'piece', 40.0, 'Large nutritious pumpkins', '../images/pumpkins.jpg'],

    // Legumes (Category ID: 5)
    ['Groundnuts', 5, 40.00, 'kg', 65.0, 'Fresh groundnuts', '../images/groundnuts.jpg'],
    ['Beans', 5, 35.00, 'kg', 80.0, 'Locally grown beans', '../images/beans.jpg'],
    ['Green Peas', 5, 30.00, 'kg', 55.0, 'Fresh green peas', '../images/peas.jpg'],
    ['Cow Peas', 5, 28.00, 'kg', 60.0, 'Nutritious cow peas', '../images/cowpeas.jpg'],
    ['Lentils', 5, 45.00, 'kg', 40.0, 'Imported lentils', '../images/lentils.jpg'],

    // Herbs (Category ID: 6)
    ['Chili Peppers', 6, 25.00, 'kg', 30.0, 'Hot chili peppers', '../images/chili.jpg'],
    ['Garlic', 6, 35.00, 'kg', 25.0, 'Fresh garlic bulbs', '../images/garlic.jpg'],
    ['Ginger', 6, 30.00, 'kg', 35.0, 'Fresh ginger roots', '../images/ginger.jpg'],
    ['Turmeric', 6, 40.00, 'kg', 20.0, 'Fresh turmeric roots', '../images/turmeric.jpg'],
    ['Mint', 6, 15.00, 'bunch', 40.0, 'Fresh mint leaves', '../images/mint.jpg']
];

// Check if farmer with ID 1 exists
$check_farmer = "SELECT id FROM farmer_profiles WHERE id = 1";
$farmer_result = mysqli_query($conn, $check_farmer);

if (mysqli_num_rows($farmer_result) == 0) {
    echo "Creating test farmer user and profile first...<br>";
    
    // Create test user
    $create_user = "INSERT INTO users (username, email, password, first_name, last_name, phone, user_type, location) 
                   VALUES ('testfarmer', 'farmer@agricoollink.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Farmer', '0971234567', 'farmer', 'Chipata, Zambia')";
    
    if (mysqli_query($conn, $create_user)) {
        $user_id = mysqli_insert_id($conn);
        echo "Created test farmer user with ID: $user_id<br>";
        
        // Create farmer profile
        $create_profile = "INSERT INTO farmer_profiles (user_id, farm_name, farm_size, farm_size_unit, farm_location, primary_produce) 
                          VALUES ($user_id, 'Green Fields Farm', 5.5, 'hectares', 'Chipata, Eastern Province', 'Tomatoes, Cabbage, Maize')";
        
        if (mysqli_query($conn, $create_profile)) {
            echo "Created test farmer profile with ID: " . mysqli_insert_id($conn) . "<br>";
        } else {
            echo "Error creating farmer profile: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Error creating test user: " . mysqli_error($conn) . "<br>";
    }
}

// Add products
$added = 0;
$farmer_id = 1; // Using farmer with ID 1
$status = 'available';
$today = date('Y-m-d');

foreach ($products as $product) {
    $name = $product[0];
    $category_id = $product[1];
    $price = $product[2];
    $unit = $product[3];
    $quantity = $product[4];
    $description = $product[5];
    $image_url = $product[6];
    
    // Check if product already exists
    $check_sql = "SELECT id FROM products WHERE name = ? AND farmer_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "si", $name, $farmer_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
    
    if (mysqli_stmt_num_rows($check_stmt) == 0) {
        // Insert new product
        $sql = "INSERT INTO products (farmer_id, category_id, name, description, price, unit, quantity, image_url, created_at, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iisdsdssss", $farmer_id, $category_id, $name, $description, $price, $unit, $quantity, $image_url, $today, $status);
        
        if (mysqli_stmt_execute($stmt)) {
            $added++;
            echo "Added product: $name<br>";
        } else {
            echo "Error adding $name: " . mysqli_error($conn) . "<br>";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Product $name already exists, skipping.<br>";
    }
    
    mysqli_stmt_close($check_stmt);
}

echo "<br>Successfully added $added new products to the database.<br>";
echo "<p>Click <a href='pages/marketplace.php'>here</a> to visit the marketplace!</p>";

mysqli_close($conn);
?>
