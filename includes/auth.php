<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/AgriCool_Link/config/database.php';

/**
 * Register a new user
 * 
 * @param string $username Username
 * @param string $email Email
 * @param string $password Password
 * @param string $first_name First name
 * @param string $last_name Last name
 * @param string $phone Phone number
 * @param string $user_type User type (farmer, buyer, storage_provider, admin)
 * @param string $location Location
 * @return array Result of registration [success => bool, message => string, user_id => int]
 */
function registerUser($username, $email, $password, $first_name, $last_name, $phone, $user_type, $location = null) {
    global $conn;
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($first_name) || 
        empty($last_name) || empty($phone) || empty($user_type)) {
        return ['success' => false, 'message' => 'All required fields must be filled'];
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    // Check if username or email already exists
    $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['username'] === $username) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        if ($row['email'] === $email) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $insert_sql = "INSERT INTO users (username, email, password, first_name, last_name, phone, user_type, location) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($stmt, "ssssssss", $username, $email, $hashed_password, $first_name, $last_name, $phone, $user_type, $location);
    
    if (mysqli_stmt_execute($stmt)) {
        $user_id = mysqli_insert_id($conn);
        
        // Create profile based on user type
        switch ($user_type) {
            case 'farmer':
                $profile_sql = "INSERT INTO farmer_profiles (user_id) VALUES (?)";
                break;
            case 'storage_provider':
                $profile_sql = "INSERT INTO storage_provider_profiles (user_id, company_name, company_address, company_phone, company_email) 
                               VALUES (?, ?, ?, ?, ?)";
                break;
            default:
                // No specific profile needed for buyers and admins
                return ['success' => true, 'message' => 'Registration successful', 'user_id' => $user_id];
        }
        
        if ($user_type === 'farmer') {
            $stmt = mysqli_prepare($conn, $profile_sql);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
        } elseif ($user_type === 'storage_provider') {
            // For storage providers, use first_name as company name initially
            $company_name = $first_name . " " . $last_name;
            $company_address = $location;
            $company_phone = $phone;
            $company_email = $email;
            
            $stmt = mysqli_prepare($conn, $profile_sql);
            mysqli_stmt_bind_param($stmt, "issss", $user_id, $company_name, $company_address, $company_phone, $company_email);
            mysqli_stmt_execute($stmt);
        }
        
        return ['success' => true, 'message' => 'Registration successful', 'user_id' => $user_id];
    } else {
        return ['success' => false, 'message' => 'Registration failed: ' . mysqli_error($conn)];
    }
}

/**
 * Login a user
 * 
 * @param string $username_or_email Username or email
 * @param string $password Password
 * @return array Result of login [success => bool, message => string, user_data => array]
 */
function loginUser($username_or_email, $password) {
    global $conn;
    
    // Validate inputs
    if (empty($username_or_email) || empty($password)) {
        return ['success' => false, 'message' => 'Username/email and password are required'];
    }
    
    // Get user from database
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username_or_email, $username_or_email);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['logged_in'] = true;
            
            // Return user data excluding password
            unset($user['password']);
            return ['success' => true, 'message' => 'Login successful', 'user_data' => $user];
        } else {
            return ['success' => false, 'message' => 'Invalid password'];
        }
    } else {
        return ['success' => false, 'message' => 'User not found'];
    }
}

/**
 * Check if user is logged in
 * 
 * @return bool Whether user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Get current user data
 * 
 * @return array|false User data or false if not logged in
 */
function getCurrentUser() {
    global $conn;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    $user_id = $_SESSION['user_id'];
    
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        unset($user['password']); // Don't send password to client
        return $user;
    }
    
    return false;
}

/**
 * Check if current user has a specific role
 * 
 * @param string|array $allowed_types Single user type or array of user types
 * @return bool Whether current user has the required role
 */
function checkUserRole($allowed_types) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $user_type = $_SESSION['user_type'];
    
    if (is_array($allowed_types)) {
        return in_array($user_type, $allowed_types);
    } else {
        return $user_type === $allowed_types;
    }
}

/**
 * Logout current user
 */
function logoutUser() {
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
}

/**
 * Redirect user based on user type
 */
function redirectBasedOnUserType() {
    if (!isLoggedIn()) {
        header('Location: /AgriCool_Link/index.php');
        exit;
    }
    
    $user_type = $_SESSION['user_type'];
    
    switch ($user_type) {
        case 'farmer':
            header('Location: /AgriCool_Link/pages/dashboard-farmer.php');
            break;
        case 'buyer':
            header('Location: /AgriCool_Link/pages/dashboard-buyer.php');
            break;
        case 'storage_provider':
            header('Location: /AgriCool_Link/pages/dashboard-storage.php');
            break;
        case 'admin':
            header('Location: /AgriCool_Link/admin/dashboard.php');
            break;
        default:
            header('Location: /AgriCool_Link/index.php');
    }
    exit;
}
?>
