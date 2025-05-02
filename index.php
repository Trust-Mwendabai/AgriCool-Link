<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include authentication functions
require_once __DIR__ . '/includes/auth.php';

// Set page title
$page_title = "AgriCool Link - Connecting Farmers to Markets";

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
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-leaf-fill"></i> AgriCool Link
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pages/marketplace.php">Marketplace</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#storage">Cold Storage</a>
                    </li>
                    <?php if ($logged_in): ?>
                        <?php if ($user_type === 'farmer'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/dashboard-farmer.php">Dashboard</a>
                        </li>
                        <?php elseif ($user_type === 'storage_provider'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/dashboard-storage.php">Dashboard</a>
                        </li>
                        <?php elseif ($user_type === 'buyer'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="pages/dashboard-buyer.php">Dashboard</a>
                        </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#dashboard">Dashboard</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <?php if ($logged_in): ?>
                    <div class="d-flex align-items-center">
                        <span class="text-white me-3">Welcome, <?php echo htmlspecialchars($username); ?></span>
                        <a href="includes/logout.php" class="btn btn-outline-light">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                <?php else: ?>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-person"></i> Login
                        </button>
                        <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="bi bi-person-plus"></i> Register
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section id="home" class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold mb-4">Preserve More, Sell Better, and Earn Bigger</h1>
                        <p class="lead mb-4">AgriCool Link connects Zambian farmers with reliable cold storage and direct market access. Reduce waste, increase profits, and grow your agricultural business.</p>
                        <div class="d-grid gap-3 d-md-flex justify-content-md-start">
                            <button class="btn btn-success btn-lg px-4">
                                <i class="bi bi-search me-2"></i> Find Storage
                            </button>
                            <button class="btn btn-outline-success btn-lg px-4">
                                <i class="bi bi-cart me-2"></i> Sell Produce
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="images/farming-in-zambia.jpg" alt="Farming in Zambia" class="img-fluid rounded-4 shadow">
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-5">
            <div class="container">
                <h2 class="text-center display-5 mb-5">Why Choose AgriCool Link?</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-thermometer-snow feature-icon"></i>
                                <h3 class="card-title h4">Cold Storage Solutions</h3>
                                <p class="card-text text-muted">Access reliable cold storage facilities to reduce post-harvest losses and maintain produce quality.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-graph-up feature-icon"></i>
                                <h3 class="card-title h4">Better Market Prices</h3>
                                <p class="card-text text-muted">Connect directly with buyers and get fair market prices without middlemen cutting into your profits.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-shop feature-icon"></i>
                                <h3 class="card-title h4">Direct Market Access</h3>
                                <p class="card-text text-muted">Reach large buyers like supermarkets, food processors, and exporters directly.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Marketplace Preview Section -->
        <section class="marketplace-preview py-5">
            <div class="container">
                <h2 class="text-center mb-4">Featured Products</h2>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 product-card">
                            <img src="images/sweetpotatoes.jpg" class="card-img-top" alt="Sweet Potatoes">
                            <div class="card-body">
                                <h5 class="card-title">Fresh Sweet Potatoes</h5>
                                <p class="card-text">High-quality sweet potatoes from local farmers. Rich in nutrients and perfect for various dishes.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">K25/kg</h5>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 product-card">
                            <img src="images/rape.jpg" class="card-img-top" alt="Fresh Rape">
                            <div class="card-body">
                                <h5 class="card-title">Fresh Rape Vegetables</h5>
                                <p class="card-text">Locally grown rape vegetables, freshly harvested and rich in vitamins.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">K15/bundle</h5>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 product-card">
                            <img src="images/tomatoes.jpg" class="card-img-top" alt="Fresh Tomatoes">
                            <div class="card-body">
                                <h5 class="card-title">Fresh Tomatoes</h5>
                                <p class="card-text">Premium quality tomatoes, perfect for salads and cooking.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">K20/kg</h5>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 product-card">
                            <img src="images/onions.jpg" class="card-img-top" alt="Fresh Onions">
                            <div class="card-body">
                                <h5 class="card-title">Fresh Onions</h5>
                                <p class="card-text">High-quality onions from local farmers, perfect for everyday cooking.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">K18/kg</h5>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mt-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 product-card">
                            <img src="images/cabbage.jpg" class="card-img-top" alt="Fresh Cabbage">
                            <div class="card-body">
                                <h5 class="card-title">Fresh Cabbage</h5>
                                <p class="card-text">Crispy and fresh cabbage, perfect for salads and cooking.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">K12/head</h5>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 product-card">
                            <img src="images/carrots.jpg" class="card-img-top" alt="Fresh Carrots">
                            <div class="card-body">
                                <h5 class="card-title">Fresh Carrots</h5>
                                <p class="card-text">Sweet and crunchy carrots, rich in vitamins and minerals.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">K16/kg</h5>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 product-card">
                            <img src="images/potatoes.jpg" class="card-img-top" alt="Fresh Potatoes">
                            <div class="card-body">
                                <h5 class="card-title">Fresh Potatoes</h5>
                                <p class="card-text">Premium quality potatoes, perfect for various cooking needs.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">K22/kg</h5>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 product-card">
                            <img src="images/chinese.jpg" class="card-img-top" alt="Chinese Cabbage">
                            <div class="card-body">
                                <h5 class="card-title">Chinese Cabbage</h5>
                                <p class="card-text">Fresh Chinese cabbage, perfect for stir-fries and salads.</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">K14/head</h5>
                                    <button class="btn btn-outline-success">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="pages/marketplace.php" class="btn btn-primary">View All Products</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-circle me-2"></i>Login to AgriCool Link
                    </h5>
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
                    <form action="includes/process_login.php" method="post">
                        <div class="mb-4">
                            <label for="username_email" class="form-label fw-semibold">Username or Email</label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-person-fill"></i></span>
                                <input type="text" class="form-control border-start-0" id="username_email" name="username_email" placeholder="Enter your username or email" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <a href="#" class="text-decoration-none small text-success">Forgot Password?</a>
                            </div>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg py-3 fw-bold">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>
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
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus-fill me-2"></i>Register with AgriCool Link
                    </h5>
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
                    <form action="includes/process_register.php" method="post" class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-semibold">First Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-person-fill"></i></span>
                                <input type="text" class="form-control border-start-0" id="first_name" name="first_name" placeholder="Enter your first name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-semibold">Last Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-person-badge-fill"></i></span>
                                <input type="text" class="form-control border-start-0" id="last_name" name="last_name" placeholder="Enter your last name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-at"></i></span>
                                <input type="text" class="form-control border-start-0" id="username" name="username" placeholder="Choose a username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="your.email@example.com" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-telephone-fill"></i></span>
                                <input type="tel" class="form-control border-start-0" id="phone" name="phone" placeholder="e.g., 097XXXXXXX" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label fw-semibold">Location</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-geo-alt-fill"></i></span>
                                <input type="text" class="form-control border-start-0" id="location" name="location" placeholder="e.g., Lusaka, Zambia" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="reg_password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" class="form-control border-start-0" id="reg_password" name="password" placeholder="Create a strong password" required>
                                <button class="btn btn-outline-secondary border-start-0" type="button" id="toggleRegPassword">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-shield-lock-fill"></i></span>
                                <input type="password" class="form-control border-start-0" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="user_type" class="form-label fw-semibold">Register as:</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-person-lines-fill"></i></span>
                                <select class="form-select border-start-0" id="user_type" name="user_type" required>
                                    <option value="" selected disabled>Choose your account type...</option>
                                    <option value="farmer">Farmer (Sell products and access cold storage)</option>
                                    <option value="buyer">Buyer (Purchase agricultural products)</option>
                                    <option value="storage_provider">Storage Provider (Offer cold storage facilities)</option>
                                </select>
                            </div>
                        </div>
                        <!-- Conditional fields for farmer -->
                        <div class="col-12 farmer-fields d-none">
                            <div class="card mb-3 shadow-sm border-success">
                                <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
                                    <i class="bi bi-tree-fill me-2"></i>Farmer Details
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="farm_name" class="form-label fw-semibold">Farm Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-house-fill"></i></span>
                                                <input type="text" class="form-control border-start-0" id="farm_name" name="farm_name" placeholder="Enter your farm name">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="farm_size" class="form-label fw-semibold">Farm Size</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-rulers"></i></span>
                                                <input type="number" step="0.01" class="form-control border-start-0" id="farm_size" name="farm_size" placeholder="Size">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="farm_size_unit" class="form-label fw-semibold">Unit</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-grid-3x3"></i></span>
                                                <select class="form-select border-start-0" id="farm_size_unit" name="farm_size_unit">
                                                    <option value="hectares">Hectares</option>
                                                    <option value="acres">Acres</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="primary_produce" class="form-label fw-semibold">Primary Produce</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-success border-end-0"><i class="bi bi-basket-fill"></i></span>
                                                <input type="text" class="form-control border-start-0" id="primary_produce" name="primary_produce" placeholder="e.g., Tomatoes, Maize, Beans">
                                            </div>
                                            <small class="text-muted">List the main crops you grow (separated by commas)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Conditional fields for storage provider -->
                        <div class="col-12 storage-fields d-none">
                            <div class="card mb-3 shadow-sm border-info">
                                <div class="card-header bg-info bg-opacity-10 text-info fw-bold">
                                    <i class="bi bi-thermometer-snow me-2"></i>Storage Provider Details
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="company_name" class="form-label fw-semibold">Company Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-info border-end-0"><i class="bi bi-building"></i></span>
                                                <input type="text" class="form-control border-start-0" id="company_name" name="company_name" placeholder="Enter your company name">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="company_address" class="form-label fw-semibold">Company Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-info border-end-0"><i class="bi bi-pin-map-fill"></i></span>
                                                <textarea class="form-control border-start-0" id="company_address" name="company_address" rows="2" placeholder="Enter your company's full address"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="company_phone" class="form-label fw-semibold">Company Phone</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-info border-end-0"><i class="bi bi-telephone-fill"></i></span>
                                                <input type="tel" class="form-control border-start-0" id="company_phone" name="company_phone" placeholder="Business contact number">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="company_email" class="form-label fw-semibold">Company Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-info border-end-0"><i class="bi bi-envelope-fill"></i></span>
                                                <input type="email" class="form-control border-start-0" id="company_email" name="company_email" placeholder="Business email address">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="has_power_backup" name="has_power_backup" value="1">
                                                <label class="form-check-label" for="has_power_backup">
                                                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Facility has power backup
                                                </label>
                                                <small class="d-block text-muted">Important for ensuring reliable cold storage during power outages</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow">
                                <i class="bi bi-person-plus me-2"></i>Create Account
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <p class="mb-0">Already have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Login</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide conditional fields based on user type selection
        const userTypeSelect = document.getElementById('user_type');
        const farmerFields = document.querySelector('.farmer-fields');
        const storageFields = document.querySelector('.storage-fields');
        
        if (userTypeSelect) {
            userTypeSelect.addEventListener('change', function() {
                if (this.value === 'farmer') {
                    farmerFields.classList.remove('d-none');
                    storageFields.classList.add('d-none');
                } else if (this.value === 'storage_provider') {
                    storageFields.classList.remove('d-none');
                    farmerFields.classList.add('d-none');
                } else {
                    farmerFields.classList.add('d-none');
                    storageFields.classList.add('d-none');
                }
            });
        }
        
        // Password toggle functionality for login form
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        
        if (togglePassword && passwordField) {
            togglePassword.addEventListener('click', function() {
                // Toggle the password field type
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                
                // Toggle the eye icon
                this.querySelector('i').classList.toggle('bi-eye');
                this.querySelector('i').classList.toggle('bi-eye-slash');
            });
        }
        
        // Password toggle functionality for registration form
        const toggleRegPassword = document.getElementById('toggleRegPassword');
        const regPasswordField = document.getElementById('reg_password');
        
        if (toggleRegPassword && regPasswordField) {
            toggleRegPassword.addEventListener('click', function() {
                // Toggle the password field type
                const type = regPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
                regPasswordField.setAttribute('type', type);
                
                // Toggle the eye icon
                this.querySelector('i').classList.toggle('bi-eye');
                this.querySelector('i').classList.toggle('bi-eye-slash');
            });
        }
        
        // Password confirmation validation
        const regPassword = document.getElementById('reg_password');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (confirmPassword && regPassword) {
            // Real-time validation
            confirmPassword.addEventListener('input', function() {
                if (this.value !== regPassword.value) {
                    this.setCustomValidity('Passwords do not match');
                    this.classList.add('is-invalid');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
            
            // Also check when regPassword changes
            regPassword.addEventListener('input', function() {
                if (confirmPassword.value && confirmPassword.value !== this.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                    confirmPassword.classList.add('is-invalid');
                } else if (confirmPassword.value) {
                    confirmPassword.setCustomValidity('');
                    confirmPassword.classList.remove('is-invalid');
                    confirmPassword.classList.add('is-valid');
                }
            });
        }
        
        // Add additional form style enhancements
        const formInputs = document.querySelectorAll('.form-control');
        formInputs.forEach(input => {
            // Add focus effects
            input.addEventListener('focus', function() {
                this.closest('.input-group').classList.add('shadow-sm');
            });
            
            input.addEventListener('blur', function() {
                this.closest('.input-group').classList.remove('shadow-sm');
            });
        });
    });
    </script>

    <footer class="mt-5">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <h5>AgriCool Link</h5>
                    <p class="text-light-50">Connecting farmers to markets and cold storage solutions in Zambia.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-light"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-linkedin fs-5"></i></a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2"></i>About Us</a></li>
                        <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2"></i>Contact</a></li>
                        <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2"></i>Terms of Service</a></li>
                        <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2"></i>Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i>info@agricoollink.com</li>
                        <li class="mb-2"><i class="bi bi-phone me-2"></i>+260 777 342 846</li>
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Lusaka, Zambia</li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4 border-light">
            <div class="text-center py-3">
                <small class="text-light-50">&copy; 2025 AgriCool Link. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
