<?php
// Include auth functions
require_once 'auth.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $user_type = $_POST['user_type'] ?? '';
    $location = trim($_POST['location'] ?? '');
    
    // Validate password match
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = 'Passwords do not match';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    // Register user
    $result = registerUser($username, $email, $password, $first_name, $last_name, $phone, $user_type, $location);
    
    if ($result['success']) {
        // Registration successful, set success message
        $_SESSION['register_success'] = $result['message'];
        
        // Auto-login user
        $login_result = loginUser($username, $password);
        
        if ($login_result['success']) {
            // Redirect based on user type
            redirectBasedOnUserType();
        } else {
            // Redirect to login page
            header('Location: /AgriCool_Link/index.php?login=1');
            exit;
        }
    } else {
        // Registration failed, set error message
        $_SESSION['register_error'] = $result['message'];
        
        // Redirect back to registration page
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    // If someone tries to access this file directly
    header('Location: /AgriCool_Link/index.php');
    exit;
}
?>
