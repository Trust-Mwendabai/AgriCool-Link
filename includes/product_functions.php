<?php
// Include database configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/AgriCool_Link/config/database.php';

/**
 * Get a random Bootstrap color for category badges
 * 
 * @return string Bootstrap color class (primary, success, info, warning, danger, etc.)
 */
function getRandomBadgeColor() {
    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
    return $colors[array_rand($colors)];
}

/**
 * Get products with optional filters
 * 
 * @param array $filters Optional filters (category_id, search, location, farmer_id, price range, storage status, freshness, sort, status)
 * @param int $limit Maximum number of products to return
 * @param int $offset Offset for pagination
 * @return array Array of products
 */
function getProducts($filters = [], $limit = 20, $offset = 0) {
    global $conn;
    
    $params = [];
    $types = '';
    
    // Start building the SQL query with necessary joins for all filter types
    $sql = "SELECT DISTINCT p.*, c.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as farmer_name, 
                   fp.farm_name, u.location as farmer_location
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN farmer_profiles fp ON p.farmer_id = fp.id
            JOIN users u ON fp.user_id = u.id";
            
    // Add storage-related joins if we're filtering by storage status
    if (isset($filters['storage_status'])) {
        $sql .= " LEFT JOIN storage_units s ON p.storage_id = s.id 
                  LEFT JOIN storage_provider_profiles sp ON s.provider_id = sp.id";
    }
    
    // Add WHERE clause if filters are provided
    $where_conditions = [];
    
    // Filter by category
    if (isset($filters['category_id']) && $filters['category_id']) {
        $where_conditions[] = "p.category_id = ?";
        $params[] = $filters['category_id'];
        $types .= 'i';
    }
    
    // Filter by search term
    if (isset($filters['search']) && $filters['search']) {
        $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
        $search_param = '%' . $filters['search'] . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'sss';
    }
    
    // Filter by location
    if (isset($filters['location']) && $filters['location']) {
        $where_conditions[] = "(u.location LIKE ? OR fp.farm_location LIKE ?)";
        $location_param = '%' . $filters['location'] . '%';
        $params[] = $location_param;
        $params[] = $location_param;
        $types .= 'ss';
    }
    
    // Filter by farmer ID
    if (isset($filters['farmer_id']) && $filters['farmer_id']) {
        $where_conditions[] = "p.farmer_id = ?";
        $params[] = $filters['farmer_id'];
        $types .= 'i';
    }
    
    // Filter by price range
    if (isset($filters['min_price']) && isset($filters['max_price'])) {
        $where_conditions[] = "p.price BETWEEN ? AND ?";
        $params[] = $filters['min_price'];
        $params[] = $filters['max_price'];
        $types .= 'dd';
    }
    
    // Filter by storage status
    if (isset($filters['storage_status'])) {
        if ($filters['storage_status'] == 'cold_storage') {
            $where_conditions[] = "p.storage_id IS NOT NULL";
        } elseif ($filters['storage_status'] == 'power_backup') {
            $where_conditions[] = "p.storage_id IS NOT NULL AND sp.has_power_backup = 1";
        } elseif ($filters['storage_status'] == 'no_storage') {
            $where_conditions[] = "p.storage_id IS NULL";
        }
    }
    
    // Filter by freshness (days since harvest)
    if (isset($filters['freshness']) && $filters['freshness']) {
        $date_threshold = date('Y-m-d', strtotime("-{$filters['freshness']} days"));
        $where_conditions[] = "p.harvest_date >= ?";
        $params[] = $date_threshold;
        $types .= 's';
    }
    
    // Filter by status
    if (isset($filters['status']) && $filters['status']) {
        $where_conditions[] = "p.status = ?";
        $params[] = $filters['status'];
        $types .= 's';
    }
    
    // Combine WHERE conditions
    if (!empty($where_conditions)) {
        $sql .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    // Add sorting
    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'price-low':
                $sql .= ' ORDER BY p.price ASC';
                break;
            case 'price-high':
                $sql .= ' ORDER BY p.price DESC';
                break;
            case 'newest':
                $sql .= ' ORDER BY p.created_at DESC';
                break;
            case 'popular':
            default:
                $sql .= ' ORDER BY p.id DESC'; // Default sorting
                break;
        }
    } else {
        $sql .= ' ORDER BY p.id DESC'; // Default sorting
    }
    
    // Add limit and offset for pagination
    $sql .= ' LIMIT ?, ?';
    $params[] = $offset;
    $params[] = $limit;
    $types .= 'ii';
    
    // Prepare and execute statement
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    return $products;
}

/**
 * Get a single product by ID
 * 
 * @param int $product_id Product ID
 * @return array|false Product data or false if not found
 */
function getProductById($product_id) {
    global $conn;
    
    $sql = "
        SELECT p.*, c.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
               fp.farm_name, fp.farm_location, u.phone as farmer_phone
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN farmer_profiles fp ON p.farmer_id = fp.id
        JOIN users u ON fp.user_id = u.id
        WHERE p.id = ?
    ";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}

/**
 * Get all categories
 * 
 * @return array Array of categories
 */
function getCategories() {
    global $conn;
    
    $sql = "SELECT * FROM categories ORDER BY name";
    $result = mysqli_query($conn, $sql);
    
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    return $categories;
}

/**
 * Get products by category
 * 
 * @param int $category_id Category ID
 * @param int $limit Maximum number of products to return
 * @return array Array of products
 */
function getProductsByCategory($category_id, $limit = 8) {
    return getProducts(['category_id' => $category_id], $limit);
}

/**
 * Get featured products for homepage
 * 
 * @param int $limit Maximum number of products to return
 * @return array Array of products
 */
function getFeaturedProducts($limit = 8) {
    return getProducts(['status' => 'available'], $limit);
}

/**
 * Add a new product
 * 
 * @param array $product_data Product data
 * @return array Result of product addition [success => bool, message => string, product_id => int]
 */
function addProduct($product_data) {
    global $conn;
    
    // Validate required fields
    $required_fields = ['farmer_id', 'category_id', 'name', 'price', 'unit', 'quantity'];
    foreach ($required_fields as $field) {
        if (empty($product_data[$field])) {
            return ['success' => false, 'message' => "Field {$field} is required"];
        }
    }
    
    // Build the SQL query
    $sql = "
        INSERT INTO products (
            farmer_id, category_id, name, description, price, unit, 
            quantity, image_url, harvest_date, storage_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    // Set default values for optional fields
    $description = $product_data['description'] ?? '';
    $image_url = $product_data['image_url'] ?? null;
    $harvest_date = $product_data['harvest_date'] ?? null;
    $storage_id = $product_data['storage_id'] ?? null;
    
    mysqli_stmt_bind_param(
        $stmt, 
        "iissddssis", 
        $product_data['farmer_id'],
        $product_data['category_id'],
        $product_data['name'],
        $description,
        $product_data['price'],
        $product_data['unit'],
        $product_data['quantity'],
        $image_url,
        $harvest_date,
        $storage_id
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $product_id = mysqli_insert_id($conn);
        return ['success' => true, 'message' => 'Product added successfully', 'product_id' => $product_id];
    } else {
        return ['success' => false, 'message' => 'Error adding product: ' . mysqli_error($conn)];
    }
}

/**
 * Update an existing product
 * 
 * @param int $product_id Product ID
 * @param array $product_data Product data to update
 * @return array Result of product update [success => bool, message => string]
 */
function updateProduct($product_id, $product_data) {
    global $conn;
    
    // Build the update query dynamically based on provided data
    $allowed_fields = [
        'category_id', 'name', 'description', 'price', 'unit', 
        'quantity', 'image_url', 'harvest_date', 'storage_id', 'status'
    ];
    
    $updates = [];
    $params = [];
    $types = '';
    
    foreach ($allowed_fields as $field) {
        if (isset($product_data[$field])) {
            $updates[] = "{$field} = ?";
            $params[] = $product_data[$field];
            
            // Set parameter type
            if (in_array($field, ['category_id', 'storage_id'])) {
                $types .= 'i';
            } elseif (in_array($field, ['price', 'quantity'])) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
    }
    
    if (empty($updates)) {
        return ['success' => false, 'message' => 'No fields to update'];
    }
    
    // Add product_id to params
    $params[] = $product_id;
    $types .= 'i';
    
    $sql = "UPDATE products SET " . implode(', ', $updates) . " WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'message' => 'Product updated successfully'];
    } else {
        return ['success' => false, 'message' => 'Error updating product: ' . mysqli_error($conn)];
    }
}

/**
 * Delete a product
 * 
 * @param int $product_id Product ID
 * @return array Result of product deletion [success => bool, message => string]
 */
function deleteProduct($product_id) {
    global $conn;
    
    $sql = "DELETE FROM products WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'message' => 'Product deleted successfully'];
    } else {
        return ['success' => false, 'message' => 'Error deleting product: ' . mysqli_error($conn)];
    }
}

/**
 * Handle product image upload
 * 
 * @param array $file The $_FILES array element
 * @return array Result of upload [success => bool, message => string, path => string]
 */
function uploadProductImage($file) {
    // Define upload directory
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/AgriCool_Link/uploads/products/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        return [
            'success' => false, 
            'message' => 'Upload error: ' . ($error_messages[$file['error']] ?? 'Unknown error')
        ];
    }
    
    // Check file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return [
            'success' => false, 
            'message' => 'Invalid file type. Only JPG, PNG and GIF are allowed.'
        ];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . basename($file['name']);
    $target_file = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return [
            'success' => true, 
            'message' => 'File uploaded successfully', 
            'path' => '/AgriCool_Link/uploads/products/' . $filename
        ];
    } else {
        return [
            'success' => false, 
            'message' => 'Error moving uploaded file'
        ];
    }
}

/**
 * Get farmer profile by user ID
 * 
 * @param int $user_id User ID
 * @return array|false Farmer profile or false if not found
 */
function getFarmerProfileByUserId($user_id) {
    global $conn;
    
    $sql = "SELECT * FROM farmer_profiles WHERE user_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}

/**
 * Count total products with optional filtering
 * 
 * @param array $filters Associative array of filters
 * @return int Total count of products
 */
function countProducts($filters = []) {
    global $conn;
    
    $where_conditions = [];
    $params = [];
    $types = '';
    
    // Build WHERE clause based on filters
    if (!empty($filters['category_id'])) {
        $where_conditions[] = 'p.category_id = ?';
        $params[] = $filters['category_id'];
        $types .= 'i';
    }
    
    if (!empty($filters['farmer_id'])) {
        $where_conditions[] = 'p.farmer_id = ?';
        $params[] = $filters['farmer_id'];
        $types .= 'i';
    }
    
    if (!empty($filters['status'])) {
        $where_conditions[] = 'p.status = ?';
        $params[] = $filters['status'];
        $types .= 's';
    }
    
    if (!empty($filters['search'])) {
        $where_conditions[] = '(p.name LIKE ? OR p.description LIKE ?)';
        $search_term = '%' . $filters['search'] . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= 'ss';
    }
    
    // Build the base query
    $sql = "SELECT COUNT(*) as total FROM products p";
    
    // Add WHERE clause if we have conditions
    if (!empty($where_conditions)) {
        $sql .= ' WHERE ' . implode(' AND ', $where_conditions);
    }
    
    // Prepare and execute statement
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return (int) $row['total'];
}
?>
