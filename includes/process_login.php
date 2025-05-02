<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database, authentication functions
require_once '../config/database.php';
require_once 'auth.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username_email = $_POST['username_email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate form data
    if (empty($username_email) || empty($password)) {
        $_SESSION['login_error'] = 'Please enter both username/email and password';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Attempt to login the user
    $login_result = loginUser($username_email, $password);
    
    if ($login_result['success']) {
        // Login successful, redirect to appropriate dashboard based on user type
        $user_type = $_SESSION['user_type'] ?? '';
        
        switch ($user_type) {
            case 'farmer':
                header('Location: ../pages/dashboard-farmer.php');
                break;
            case 'buyer':
                header('Location: ../pages/dashboard-buyer.php');
                break;
            case 'storage_provider':
                header('Location: ../pages/dashboard-storage.php');
                break;
            default:
                header('Location: ../index.php');
                break;
        }
        exit();
    } else {
        // Login failed, set error message and redirect back
        $_SESSION['login_error'] = $login_result['message'];
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    // If someone tries to access this file directly
    header('Location: /AgriCool_Link/index.php');
    exit;
}
?>
