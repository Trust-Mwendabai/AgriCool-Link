<?php
/**
 * Extended advanced filter functions for addressing Zambian farmers' key challenges:
 * - Post-harvest losses from inadequate cold storage
 * - Price instability and middlemen exploitation
 * - Limited market access
 * - Unreliable rural electricity affecting cold storage
 */

/**
 * Get products that are stored in cold storage facilities
 * 
 * @param int $limit Maximum number of products to return
 * @param int $offset Offset for pagination
 * @return array Array of products
 */
function getProductsInColdStorage($limit = 20, $offset = 0) {
    global $conn;
    
    $products = [];
    
    $sql = "SELECT p.*, c.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                   u.location as farmer_location, fp.farm_name, s.name as storage_name, 
                   s.temperature_range, s.has_power
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN farmer_profiles fp ON p.farmer_id = fp.id
            JOIN users u ON fp.user_id = u.id
            JOIN storage_units s ON p.storage_id = s.id
            WHERE p.storage_id IS NOT NULL
            AND p.status = 'available'
            LIMIT ? OFFSET ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return $products;
}

/**
 * Get products from facilities with reliable power backup
 * 
 * @param int $limit Maximum number of products to return
 * @param int $offset Offset for pagination
 * @return array Array of products
 */
function getProductsWithPowerBackup($limit = 20, $offset = 0) {
    global $conn;
    
    $products = [];
    
    $sql = "SELECT p.*, c.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                   u.location as farmer_location, fp.farm_name, s.name as storage_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN farmer_profiles fp ON p.farmer_id = fp.id
            JOIN users u ON fp.user_id = u.id
            JOIN storage_units s ON p.storage_id = s.id
            JOIN storage_provider_profiles sp ON s.provider_id = sp.id
            WHERE sp.has_power_backup = 1
            AND p.status = 'available'
            LIMIT ? OFFSET ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return $products;
}

/**
 * Get products within a specific price range
 * 
 * @param float $min_price Minimum price
 * @param float $max_price Maximum price
 * @param int $limit Maximum number of products to return
 * @param int $offset Offset for pagination
 * @return array Array of products
 */
function getProductsByPriceRange($min_price, $max_price, $limit = 20, $offset = 0) {
    global $conn;
    
    $products = [];
    
    $sql = "SELECT p.*, c.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                   u.location as farmer_location, fp.farm_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN farmer_profiles fp ON p.farmer_id = fp.id
            JOIN users u ON fp.user_id = u.id
            WHERE p.price BETWEEN ? AND ?
            AND p.status = 'available'
            LIMIT ? OFFSET ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ddii', $min_price, $max_price, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return $products;
}

/**
 * Get products harvested within a specific time frame (for freshness filtering)
 * 
 * @param int $days_ago Maximum days since harvest
 * @param int $limit Maximum number of products to return
 * @param int $offset Offset for pagination
 * @return array Array of products
 */
function getProductsByFreshness($days_ago, $limit = 20, $offset = 0) {
    global $conn;
    
    $products = [];
    $date_threshold = date('Y-m-d', strtotime("-$days_ago days"));
    
    $sql = "SELECT p.*, c.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                   u.location as farmer_location, fp.farm_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN farmer_profiles fp ON p.farmer_id = fp.id
            JOIN users u ON fp.user_id = u.id
            WHERE p.harvest_date >= ?
            AND p.status = 'available'
            LIMIT ? OFFSET ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'sii', $date_threshold, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return $products;
}

/**
 * Get price ranges for the marketplace filter
 * 
 * @return array Array of price ranges
 */
function getPriceRanges() {
    global $conn;
    
    // Get min and max prices
    $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE status = 'available'";
    $result = mysqli_query($conn, $sql);
    $price_data = mysqli_fetch_assoc($result);
    
    $min_price = floor($price_data['min_price']);
    $max_price = ceil($price_data['max_price']);
    
    // Create price ranges
    $ranges = [];
    
    // Handle case where there are very few products
    if ($max_price - $min_price <= 20) {
        $ranges[] = [
            'min' => $min_price,
            'max' => $max_price,
            'label' => "K$min_price - K$max_price"
        ];
        return $ranges;
    }
    
    // Calculate appropriate intervals
    $interval = ceil(($max_price - $min_price) / 5); // Create 5 ranges
    
    for ($i = 0; $i < 5; $i++) {
        $range_min = $min_price + ($i * $interval);
        $range_max = $min_price + (($i + 1) * $interval) - 1;
        
        // Ensure last range includes the max price
        if ($i == 4) {
            $range_max = $max_price;
        }
        
        $ranges[] = [
            'min' => $range_min,
            'max' => $range_max,
            'label' => "K$range_min - K$range_max"
        ];
    }
    
    return $ranges;
}

/**
 * Get available cold storage status options
 * 
 * @return array Array of storage status options
 */
function getColdStorageOptions() {
    return [
        ['value' => 'any', 'label' => 'Any Storage Status'],
        ['value' => 'cold_storage', 'label' => 'In Cold Storage Only'],
        ['value' => 'power_backup', 'label' => 'With Power Backup'],
        ['value' => 'no_storage', 'label' => 'Not in Storage']
    ];
}

/**
 * Get freshness filter options
 * 
 * @return array Array of freshness options
 */
function getFreshnessOptions() {
    return [
        ['value' => 'any', 'label' => 'Any Harvesting Time'],
        ['value' => '3', 'label' => 'Last 3 Days'],
        ['value' => '7', 'label' => 'Last Week'],
        ['value' => '14', 'label' => 'Last 2 Weeks'],
        ['value' => '30', 'label' => 'Last Month']
    ];
}
?>
