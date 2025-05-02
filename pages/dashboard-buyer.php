<?php
// Set page title
$page_title = "Buyer Dashboard - AgriCool Link";

// Include database connection
require_once '../config/database.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a buyer
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_type'] !== 'buyer') {
    // Redirect to login page
    header('Location: ../index.php');
    exit;
}

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Get order statistics
$sql = "SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
            SUM(total_amount) as total_spent
        FROM orders WHERE buyer_id = ?";
$stmt = mysqli_prepare($conn, $sql);
$order_stats = [];
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order_stats = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
if (!$order_stats) {
    $order_stats = [
        'total_orders' => 0,
        'completed_orders' => 0,
        'pending_orders' => 0,
        'processing_orders' => 0,
        'total_spent' => 0
    ];
}

// Get recent orders
$sql = "SELECT o.*, 
           (SELECT GROUP_CONCAT(p.name SEPARATOR ', ') 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = o.id LIMIT 3) as product_list
        FROM orders o 
        WHERE o.buyer_id = ? 
        ORDER BY o.created_at DESC LIMIT 5";
$stmt = mysqli_prepare($conn, $sql);
$recent_orders = [];
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_orders[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Get recommended products based on previous purchases and seasonality
$sql = "SELECT p.*, c.name as category_name, CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
               fp.farm_name, s.name as storage_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN farmer_profiles fp ON p.farmer_id = fp.id
        JOIN users u ON fp.user_id = u.id
        LEFT JOIN storage_units s ON p.storage_id = s.id
        WHERE p.status = 'available'
        AND (p.category_id IN (
            SELECT DISTINCT p2.category_id 
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p2 ON oi.product_id = p2.id
            WHERE o.buyer_id = ?
        ) OR p.is_featured = 1)
        ORDER BY RAND() LIMIT 6";
$stmt = mysqli_prepare($conn, $sql);
$recommended_products = [];
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $recommended_products[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Get favorite farmers (most ordered from)
$sql = "SELECT fp.id, fp.farm_name, CONCAT(u.first_name, ' ', u.last_name) as farmer_name,
               u.location, COUNT(DISTINCT o.id) as order_count,
               (SELECT COUNT(*) FROM products WHERE farmer_id = fp.id AND status = 'available') as available_products
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        JOIN farmer_profiles fp ON p.farmer_id = fp.id
        JOIN users u ON fp.user_id = u.id
        WHERE o.buyer_id = ?
        GROUP BY fp.id
        ORDER BY order_count DESC LIMIT 3";
$stmt = mysqli_prepare($conn, $sql);
$favorite_farmers = [];
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $favorite_farmers[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// Get monthly spending for chart
$sql = "SELECT MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as total
        FROM orders
        WHERE buyer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY YEAR(created_at), MONTH(created_at)
        ORDER BY YEAR(created_at), MONTH(created_at)";
$stmt = mysqli_prepare($conn, $sql);
$monthly_spending = [];
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $month_name = date('M', mktime(0, 0, 0, $row['month'], 1));
        $monthly_spending[$month_name] = $row['total'];
    }
    mysqli_stmt_close($stmt);
}

// Fill in any missing months from the last 6 months
$months = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('M', strtotime("-$i months"));
    $months[] = $month;
    if (!isset($monthly_spending[$month])) {
        $monthly_spending[$month] = 0;
    }
}

// Reorder by chronological month
$spending_data = [];
foreach ($months as $month) {
    $spending_data[$month] = $monthly_spending[$month];
}

// Get recent cold storage alerts
$sql = "SELECT p.name as product_name, p.image as product_image,
               s.name as storage_name, sa.message, sa.created_at
        FROM storage_alerts sa
        JOIN products p ON sa.product_id = p.id
        JOIN storage_units s ON p.storage_id = s.id
        JOIN order_items oi ON p.id = oi.product_id
        JOIN orders o ON oi.order_id = o.id
        WHERE o.buyer_id = ? AND sa.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY sa.id
        ORDER BY sa.created_at DESC LIMIT 3";
$stmt = mysqli_prepare($conn, $sql);
$storage_alerts = [];
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $storage_alerts[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <a href="../index.php" class="d-flex align-items-center text-decoration-none">
                    <i class="bi bi-leaf-fill me-2"></i>
                    <span>AgriCool Link</span>
                </a>
            </div>
            <ul class="list-unstyled sidebar-menu">
                <li class="active">
                    <a href="dashboard-buyer.php" class="menu-item">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../pages/marketplace.php" class="menu-item">
                        <i class="bi bi-shop"></i>
                        <span>Marketplace</span>
                    </a>
                </li>
                <li>
                    <a href="orders.php" class="menu-item">
                        <i class="bi bi-cart"></i>
                        <span>My Orders</span>
                    </a>
                </li>
                <li>
                    <a href="favorite-farmers.php" class="menu-item">
                        <i class="bi bi-people"></i>
                        <span>Favorite Farmers</span>
                    </a>
                </li>
                <li>
                    <a href="storage-tracking.php" class="menu-item">
                        <i class="bi bi-thermometer-snow"></i>
                        <span>Cold Storage Tracking</span>
                    </a>
                </li>
                <li>
                    <a href="spending-analytics.php" class="menu-item">
                        <i class="bi bi-graph-up"></i>
                        <span>Spending Analytics</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-item">
                        <i class="bi bi-chat-dots"></i>
                        <span>Messages</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-item">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button id="sidebarToggle" class="btn btn-link">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="d-flex align-items-center ms-auto">
                        <div class="dropdown me-3">
                            <button class="btn btn-link position-relative" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    2
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <h6 class="dropdown-header">Notifications</h6>
                                <a class="dropdown-item" href="#">Order status update</a>
                                <a class="dropdown-item" href="#">New product available</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                <img src="../images/placeholder.jpg" class="rounded-circle me-2" width="32" height="32">
                                <span>Sarah Buyer</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a>
                                <a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Dashboard Content -->
            <div class="container-fluid py-4">
                <!-- Welcome Banner -->
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</h4>
                                <p class="mb-0">Continue supporting Zambian farmers by purchasing fresh, locally grown produce.</p>
                            </div>
                            <a href="../pages/marketplace.php" class="btn btn-light">Browse Products</a>
                        </div>
                    </div>
                </div>
                
                <!-- Overview Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-box bg-primary text-white me-3">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0"><?php echo number_format($order_stats['total_orders']); ?></h5>
                                    <p class="card-text text-muted">Total Orders</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-box bg-success text-white me-3">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0"><?php echo number_format($order_stats['completed_orders']); ?></h5>
                                    <p class="card-text text-muted">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-box bg-warning text-white me-3">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0"><?php echo number_format($order_stats['pending_orders'] + $order_stats['processing_orders']); ?></h5>
                                    <p class="card-text text-muted">In Progress</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-box bg-info text-white me-3">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">K<?php echo number_format($order_stats['total_spent'], 2); ?></h5>
                                    <p class="card-text text-muted">Total Spent</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders and Product Categories -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Recent Orders</h5>
                                <button class="btn btn-sm btn-outline-primary">View All</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Product</th>
                                                <th>Supplier</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>#ORD-001</td>
                                                <td>Fresh Tomatoes</td>
                                                <td>John Farmer</td>
                                                <td>K2,500</td>
                                                <td><span class="badge bg-success">Delivered</span></td>
                                            </tr>
                                            <tr>
                                                <td>#ORD-002</td>
                                                <td>Potatoes</td>
                                                <td>Mary Gardens</td>
                                                <td>K1,800</td>
                                                <td><span class="badge bg-warning">Processing</span></td>
                                            </tr>
                                            <tr>
                                                <td>#ORD-003</td>
                                                <td>Onions</td>
                                                <td>Green Fields</td>
                                                <td>K950</td>
                                                <td><span class="badge bg-info">Shipped</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Product Categories</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="categoryChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Dashboard Content -->
                <div class="row g-4 mb-4">
                    <!-- Recent Orders Section -->
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Recent Orders</h5>
                                <a href="orders.php" class="btn btn-sm btn-outline-success">View All Orders</a>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_orders)): ?>
                                <div class="text-center py-4">
                                    <div class="mb-3">
                                        <i class="bi bi-cart text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                    <p class="text-muted">You haven't placed any orders yet.</p>
                                    <a href="../pages/marketplace.php" class="btn btn-success">Start Shopping</a>
                                </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Date</th>
                                                <th>Products</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                                        <?php echo htmlspecialchars($order['product_list']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status_class = '';
                                                    switch($order['status']) {
                                                        case 'completed':
                                                            $status_class = 'bg-success';
                                                            break;
                                                        case 'processing':
                                                            $status_class = 'bg-info';
                                                            break;
                                                        case 'pending':
                                                            $status_class = 'bg-warning';
                                                            break;
                                                        case 'cancelled':
                                                            $status_class = 'bg-danger';
                                                            break;
                                                        default:
                                                            $status_class = 'bg-secondary';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $status_class; ?>">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td>K<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i>Details
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Spending & Storage Analytics -->
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Monthly Spending</h5>
                                <a href="spending-analytics.php" class="btn btn-sm btn-outline-success">Details</a>
                            </div>
                            <div class="card-body">
                                <canvas id="spendingChart" height="250" data-spending='<?php echo json_encode($monthly_spending); ?>'></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Favorite Farmers Section -->
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Your Favorite Farmers</h5>
                                <a href="favorite-farmers.php" class="btn btn-sm btn-outline-success">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if(empty($favorite_farmers)): ?>
                                <div class="text-center py-4">
                                    <p class="text-muted">You haven't ordered from any farmers yet. Start shopping to support local Zambian farmers!</p>
                                </div>
                                <?php else: ?>
                                <div class="row g-4">
                                    <?php foreach($favorite_farmers as $farmer): ?>
                                    <div class="col-md-4">
                                        <div class="card h-100 farmer-card">
                                            <div class="card-body">
                                                <div class="d-flex mb-3 align-items-center">
                                                    <div class="avatar-bg bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                        <?php echo strtoupper(substr($farmer['farmer_name'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <h5 class="mb-0"><?php echo htmlspecialchars($farmer['farmer_name']); ?></h5>
                                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($farmer['farm_name']); ?></p>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span><i class="bi bi-geo-alt me-1"></i> Location:</span>
                                                        <span class="text-muted"><?php echo htmlspecialchars($farmer['location']); ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span><i class="bi bi-bag-check me-1"></i> Your Orders:</span>
                                                        <span class="text-muted"><?php echo $farmer['order_count']; ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span><i class="bi bi-box2 me-1"></i> Available Products:</span>
                                                        <span class="text-muted"><?php echo $farmer['available_products']; ?></span>
                                                    </div>
                                                </div>
                                                <a href="../pages/marketplace.php?farmer=<?php echo $farmer['id']; ?>" class="btn btn-sm btn-outline-success w-100">
                                                    <i class="bi bi-shop me-1"></i>View Products
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommended Products -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recommended Products</h5>
                        <a href="../pages/marketplace.php" class="btn btn-sm btn-outline-success">View All Products</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recommended_products)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No recommended products at this time. Start shopping to get personalized recommendations!</p>
                        </div>
                        <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($recommended_products as $product): ?>
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <div class="product-card">
                                    <div class="product-img-wrapper">
                                        <img src="<?php echo !empty($product['image']) ? '../uploads/products/' . $product['image'] : '../images/product-placeholder.jpg'; ?>" class="product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php if (!empty($product['storage_name'])): ?>
                                        <span class="product-badge bg-info">Cold Stored</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-details">
                                        <h6 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <p class="product-price">K<?php echo number_format($product['price'], 2); ?></p>
                                        <p class="product-seller"><?php echo htmlspecialchars($product['farm_name']); ?></p>
                                        <a href="../pages/product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-success w-100">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/dashboard-buyer.js"></script>
</body>
</html>
