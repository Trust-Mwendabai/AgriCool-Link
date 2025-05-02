<?php
// Include auth functions
require_once 'auth.php';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/AgriCool_Link/config/database.php';

// Make sure user is logged in
if (!isLoggedIn()) {
    // Redirect to login page with message
    $_SESSION['login_error'] = 'Please log in to checkout';
    header('Location: ../index.php');
    exit;
}

// Make sure user is a buyer (or allow farmer to also buy)
if (!checkUserRole(['buyer', 'farmer'])) {
    header('Location: ../index.php');
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    $cart_items = json_decode($_POST['cart_items'] ?? '[]', true);
    $subtotal = floatval($_POST['subtotal'] ?? 0);
    $shipping = floatval($_POST['shipping'] ?? 0);
    $total = floatval($_POST['total'] ?? 0);
    
    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || 
        empty($shipping_address) || empty($payment_method) || empty($cart_items)) {
        $_SESSION['checkout_error'] = 'All fields are required';
        header('Location: ../pages/checkout.php');
        exit;
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['checkout_error'] = 'Invalid email format';
        header('Location: ../pages/checkout.php');
        exit;
    }
    
    // Get buyer ID
    $buyer_id = $_SESSION['user_id'];
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert order
        $order_sql = "INSERT INTO orders (buyer_id, total_amount, shipping_address, payment_method, payment_status) 
                     VALUES (?, ?, ?, ?, 'pending')";
        
        $stmt = mysqli_prepare($conn, $order_sql);
        mysqli_stmt_bind_param($stmt, "idss", $buyer_id, $total, $shipping_address, $payment_method);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error creating order: " . mysqli_error($conn));
        }
        
        $order_id = mysqli_insert_id($conn);
        
        // Insert order items
        foreach ($cart_items as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];
            $price_per_unit = $item['price'];
            $subtotal_item = $price_per_unit * $quantity;
            
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price_per_unit, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
                        
            $stmt = mysqli_prepare($conn, $item_sql);
            mysqli_stmt_bind_param($stmt, "iiddd", $order_id, $product_id, $quantity, $price_per_unit, $subtotal_item);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error adding order item: " . mysqli_error($conn));
            }
            
            // Update product availability
            $update_product_sql = "UPDATE products 
                                  SET quantity = quantity - ?, 
                                      status = CASE WHEN quantity - ? <= 0 THEN 'sold' ELSE status END 
                                  WHERE id = ?";
                                  
            $stmt = mysqli_prepare($conn, $update_product_sql);
            mysqli_stmt_bind_param($stmt, "ddi", $quantity, $quantity, $product_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error updating product quantity: " . mysqli_error($conn));
            }
        }
        
        // If we got here, commit the transaction
        mysqli_commit($conn);
        
        // Set success message
        $_SESSION['order_success'] = true;
        $_SESSION['order_id'] = $order_id;
        
        // Redirect to order confirmation page
        header('Location: ../pages/order_confirmation.php');
        exit;
        
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        
        // Set error message
        $_SESSION['checkout_error'] = $e->getMessage();
        
        // Redirect back to checkout page
        header('Location: ../pages/checkout.php');
        exit;
    }
} else {
    // If someone tries to access this file directly
    header('Location: ../index.php');
    exit;
}
?>
