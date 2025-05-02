<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include authentication functions
require_once __DIR__ . '/auth.php';

// Check if user is logged in
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_type = $logged_in ? $_SESSION['user_type'] : '';
$username = $logged_in ? $_SESSION['username'] : '';

// Get current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'AgriCool Link'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <?php 
    // Determine the correct path for CSS files based on current directory
    $root_path = $_SERVER['DOCUMENT_ROOT'] . '/AgriCool_Link/';
    $current_path = dirname($_SERVER['SCRIPT_FILENAME']);
    $relative_path = str_replace($root_path, '', $current_path);
    $css_path = $relative_path ? '../css/' : 'css/';
    ?>
    
    <link rel="stylesheet" href="<?php echo $css_path; ?>style.css">
    <?php if (strpos($current_page, 'dashboard') !== false): ?>
    <link rel="stylesheet" href="<?php echo $css_path; ?>dashboard.css">
    <?php endif; ?>
    <?php if ($current_page === 'marketplace.php'): ?>
    <link rel="stylesheet" href="<?php echo $css_path; ?>marketplace.css">
    <link rel="stylesheet" href="<?php echo $css_path; ?>category-chips.css">
    <link rel="stylesheet" href="<?php echo $css_path; ?>filter-history.css">
    <?php endif; ?>
</head>
<body<?php echo (strpos($current_page, 'dashboard') !== false) ? ' class="dashboard-body"' : ''; ?>>
    <?php if (strpos($current_page, 'dashboard') === false): ?>
    <!-- Regular Navigation for non-dashboard pages -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $relative_path ? '../index.php' : 'index.php'; ?>">
                <i class="bi bi-leaf-fill"></i> AgriCool Link
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-4">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>" href="<?php echo $relative_path ? '../index.php' : 'index.php'; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'marketplace.php') ? 'active' : ''; ?>" href="<?php echo $relative_path ? 'marketplace.php' : 'pages/marketplace.php'; ?>">Marketplace</a>
                    </li>
                    <?php if ($logged_in): ?>
                        <?php if ($user_type === 'farmer'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $relative_path ? 'dashboard-farmer.php' : 'pages/dashboard-farmer.php'; ?>">Dashboard</a>
                        </li>
                        <?php elseif ($user_type === 'storage_provider'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $relative_path ? 'dashboard-storage.php' : 'pages/dashboard-storage.php'; ?>">Dashboard</a>
                        </li>
                        <?php elseif ($user_type === 'buyer'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $relative_path ? 'dashboard-buyer.php' : 'pages/dashboard-buyer.php'; ?>">Dashboard</a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $relative_path ? '../includes/logout.php' : 'includes/logout.php'; ?>">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-person"></i> Login
                            </button>
                        </li>
                        <li class="nav-item ms-2">
                            <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="bi bi-person-plus"></i> Register
                            </button>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <?php if (!isset($hide_modals) && !$logged_in && $current_page !== 'dashboard-farmer.php' && $current_page !== 'dashboard-storage.php' && $current_page !== 'dashboard-buyer.php'): ?>
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login to AgriCool Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($_SESSION['login_error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                            echo $_SESSION['login_error']; 
                            unset($_SESSION['login_error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['register_success'])): ?>
                        <div class="alert alert-success">
                            <?php 
                            echo $_SESSION['register_success']; 
                            unset($_SESSION['register_success']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo $relative_path ? '../includes/process_login.php' : 'includes/process_login.php'; ?>" method="post">
                        <div class="mb-3">
                            <label for="username_email" class="form-label">Username or Email</label>
                            <input type="text" class="form-control" id="username_email" name="username_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Login</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <p class="mb-0">Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Register</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register with AgriCool Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($_SESSION['register_error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                            echo $_SESSION['register_error']; 
                            unset($_SESSION['register_error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo $relative_path ? '../includes/process_register.php' : 'includes/process_register.php'; ?>" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="City, Province">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">I am registering as a:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="user_type" id="farmer" value="farmer" checked>
                                <label class="form-check-label" for="farmer">
                                    Farmer - I want to sell produce and/or find storage
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="user_type" id="buyer" value="buyer">
                                <label class="form-check-label" for="buyer">
                                    Buyer - I want to purchase agricultural products
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="user_type" id="storage_provider" value="storage_provider">
                                <label class="form-check-label" for="storage_provider">
                                    Storage Provider - I offer cold storage facilities
                                </label>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Register</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <p class="mb-0">Already have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Login</a></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</script>
</script>
</script>
</script>
</script>
</script>
</script>
<?php if ($logged_in && $current_page === 'index.php'): ?>
<!-- Cart Modal for Marketplace -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cart"></i> Your Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="cart-items">
                    <!-- Cart items will be loaded here via JavaScript -->
                    <div class="text-center py-5" id="empty-cart-message">
                        <i class="bi bi-cart3 text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-3">Your cart is empty</p>
                        <a href="<?php echo $relative_path ? 'marketplace.php' : 'pages/marketplace.php'; ?>" class="btn btn-success mt-2">
                            <i class="bi bi-shop"></i> Browse Marketplace
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <strong>Total: </strong><span id="cart-total">K0.00</span>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                    <button type="button" class="btn btn-success" id="checkout-btn" disabled>Proceed to Checkout</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
</script>
</script>
</script>
</script>
</script>
</script>
</html>
