<?php
/**
 * Advanced filter functions for the marketplace
 * 
 * These functions provide additional filtering capabilities to help address key
 * challenges facing Zambian farmers:
 * - Post-harvest losses from inadequate cold storage
 * - Price instability and middlemen exploitation
 * - Limited market access
 * - Unreliable rural electricity affecting cold storage
 */

/**
 * Get all distinct locations from farmers and users
 * 
 * @return array Array of locations
 */
function getDistinctLocations() {
    global $conn;
    
    $locations = [];
    
    // Get locations from users table
    $sql = "SELECT DISTINCT location FROM users WHERE location != '' ORDER BY location";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['location']) && !in_array($row['location'], $locations)) {
                $locations[] = $row['location'];
            }
        }
    }
    
    // Get farm locations from farmer_profiles table
    $sql = "SELECT DISTINCT farm_location FROM farmer_profiles WHERE farm_location != '' ORDER BY farm_location";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['farm_location']) && !in_array($row['farm_location'], $locations)) {
                $locations[] = $row['farm_location'];
            }
        }
    }
    
    return $locations;
}

/**
 * Get all farmers (name and ID)
 * 
 * @return array Array of farmers
 */
function getAllFarmers() {
    global $conn;
    
    $farmers = [];
    
    $sql = "SELECT f.id, f.farm_name, CONCAT(u.first_name, ' ', u.last_name) AS farmer_name
            FROM farmer_profiles f 
            JOIN users u ON f.user_id = u.id
            ORDER BY farmer_name";
            
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $farmers[] = $row;
        }
    }
    
    return $farmers;
}

/**
 * Filter products by location
 * 
 * @param string $location Location to filter by
 * @param int $limit Maximum number of products to return
 * @param int $offset Offset for pagination
 * @return array Array of products
 */
function getProductsByLocation($location, $limit = 20, $offset = 0) {
    global $conn;
    
    $products = [];
    $params = [];
    $types = '';
    
    $sql = "SELECT p.*, c.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                   u.location as farmer_location, fp.farm_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN farmer_profiles fp ON p.farmer_id = fp.id
            JOIN users u ON fp.user_id = u.id
            WHERE (u.location LIKE ? OR fp.farm_location LIKE ?)
            AND p.status = 'available'
            ORDER BY p.name
            LIMIT ? OFFSET ?";
    
    $location_param = '%' . $location . '%';
    $params = [$location_param, $location_param, $limit, $offset];
    $types = 'ssii';
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
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
 * Filter products by farmer
 * 
 * @param int $farmer_id Farmer ID to filter by
 * @param int $limit Maximum number of products to return
 * @param int $offset Offset for pagination
 * @return array Array of products
 */
function getProductsByFarmer($farmer_id, $limit = 20, $offset = 0) {
    global $conn;
    
    $products = [];
    
    $sql = "SELECT p.*, c.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
                   u.location as farmer_location, fp.farm_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN farmer_profiles fp ON p.farmer_id = fp.id
            JOIN users u ON fp.user_id = u.id
            WHERE p.farmer_id = ?
            AND p.status = 'available'
            ORDER BY p.name
            LIMIT ? OFFSET ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'iii', $farmer_id, $limit, $offset);
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
 * Count products by location for pagination
 * 
 * @param string $location Location to filter by
 * @return int Number of products
 */
function countProductsByLocation($location) {
    global $conn;
    
    $count = 0;
    
    $sql = "SELECT COUNT(*) as total
            FROM products p
            JOIN farmer_profiles fp ON p.farmer_id = fp.id
            JOIN users u ON fp.user_id = u.id
            WHERE (u.location LIKE ? OR fp.farm_location LIKE ?)
            AND p.status = 'available'";
    
    $location_param = '%' . $location . '%';
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $location_param, $location_param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $count = $row['total'];
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return $count;
}

/**
 * Count products by farmer for pagination
 * 
 * @param int $farmer_id Farmer ID to filter by
 * @return int Number of products
 */
function countProductsByFarmer($farmer_id) {
    global $conn;
    
    $count = 0;
    
    $sql = "SELECT COUNT(*) as total
            FROM products p
            WHERE p.farmer_id = ?
            AND p.status = 'available'";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $farmer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $count = $row['total'];
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return $count;
}
?>
