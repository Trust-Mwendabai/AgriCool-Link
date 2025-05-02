<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - AgriCool Link</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar bg-dark">
            <a href="../index.php" class="navbar-brand text-white">
                <i class="fas fa-leaf"></i> AgriCool Link
            </a>
            <nav class="nav flex-column">
                <a class="nav-link text-white" href="#overview">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a class="nav-link text-white" href="#upload">
                    <i class="fas fa-box"></i> Products
                </a>
                <a class="nav-link text-white" href="#storage">
                    <i class="fas fa-warehouse"></i> Storage
                </a>
                <a class="nav-link text-white" href="#analytics">
                    <i class="fas fa-chart-line"></i> Analytics
                </a>
                <a class="nav-link text-white" href="#settings">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a class="nav-link text-white" href="#help">
                    <i class="fas fa-question-circle"></i> Help
                </a>
                <a class="nav-link text-danger" href="../index.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

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
                                    3
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <h6 class="dropdown-header">Notifications</h6>
                                <a class="dropdown-item" href="#">New order received</a>
                                <a class="dropdown-item" href="#">Storage unit alert</a>
                                <a class="dropdown-item" href="#">Price update alert</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                <img src="../images/placeholder.jpg" class="rounded-circle me-2" width="32" height="32">
                                <span>John Farmer</span>
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

            <!-- Dashboard Tabs -->
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab" aria-controls="upload" aria-selected="false">Upload Product</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="storage-tab" data-bs-toggle="tab" data-bs-target="#storage" type="button" role="tab" aria-controls="storage" aria-selected="false">Storage</button>
                </li>
            </ul>
            <div class="tab-content" id="dashboardTabsContent">
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <!-- Overview Content -->
                    <div class="container-fluid py-4">
                        <!-- Overview Cards -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="stats-icon bg-primary">
                                                    <i class="bi bi-box-seam text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Products</h6>
                                                <span class="text-muted">120</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="stats-icon bg-success">
                                                    <i class="bi bi-people text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Customers</h6>
                                                <span class="text-muted">85</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="stats-icon bg-warning">
                                                    <i class="bi bi-graph-up text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Sales</h6>
                                                <span class="text-muted">ZMW 12,500</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="stats-icon bg-danger">
                                                    <i class="bi bi-exclamation-triangle text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Alerts</h6>
                                                <span class="text-muted">3</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activities -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Recent Activities</h5>
                                        <button class="btn btn-sm btn-outline-primary" type="button">View All</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="activity-item d-flex align-items-center mb-3">
                                            <div class="activity-icon bg-success me-3">
                                                <i class="bi bi-check-circle text-white"></i>
                                            </div>
                                            <div class="activity-content">
                                                <h6 class="mb-1">Order #123 completed</h6>
                                                <small class="text-muted">2 hours ago</small>
                                            </div>
                                        </div>
                                        <div class="activity-item d-flex align-items-center mb-3">
                                            <div class="activity-icon bg-warning me-3">
                                                <i class="bi bi-exclamation-circle text-white"></i>
                                            </div>
                                            <div class="activity-content">
                                                <h6 class="mb-1">Storage temperature alert</h6>
                                                <small class="text-muted">5 hours ago</small>
                                            </div>
                                        </div>
                                        <div class="activity-item d-flex align-items-center">
                                            <div class="activity-icon bg-info me-3">
                                                <i class="bi bi-cart-plus text-white"></i>
                                            </div>
                                            <div class="activity-content">
                                                <h6 class="mb-1">New order received</h6>
                                                <small class="text-muted">1 day ago</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Storage Status -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Storage Status</h5>
                                        <button class="btn btn-sm btn-outline-primary" type="button">Manage Storage</button>
                                    </div>
                                    <div class="card-body">
                                        <p>Storage details and management options will be displayed here.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                    <!-- Product Upload Form -->
                    <div class="container-fluid py-4">
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Upload New Product</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="productUploadForm" class="row g-3">
                                            <div class="col-md-6">
                                                <label for="productName" class="form-label">Product Name</label>
                                                <input type="text" class="form-control" id="productName" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="category" class="form-label">Category</label>
                                                <select class="form-select" id="category" required>
                                                    <option value="">Choose category...</option>
                                                    <option value="vegetables">Vegetables</option>
                                                    <option value="fruits">Fruits</option>
                                                    <option value="grains">Grains</option>
                                                    <option value="dairy">Dairy</option>
                                                    <option value="meat">Meat</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="price" class="form-label">Price (ZMW)</label>
                                                <input type="number" class="form-control" id="price" min="0" step="0.01" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="quantity" class="form-label">Quantity</label>
                                                <input type="number" class="form-control" id="quantity" min="1" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="unit" class="form-label">Unit</label>
                                                <select class="form-select" id="unit" required>
                                                    <option value="">Choose unit...</option>
                                                    <option value="kg">Kilograms (kg)</option>
                                                    <option value="g">Grams (g)</option>
                                                    <option value="pieces">Pieces</option>
                                                    <option value="boxes">Boxes</option>
                                                    <option value="bags">Bags</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea class="form-control" id="description" rows="3" required></textarea>
                                            </div>
                                            <div class="col-12">
                                                <label for="productImage" class="form-label">Product Image</label>
                                                <input type="file" class="form-control" id="productImage" accept="image/*" required>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-cloud-upload"></i> Upload Product
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="storage" role="tabpanel" aria-labelledby="storage-tab">
                    <!-- Storage Content -->
                    <div class="container-fluid py-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Storage Status</h5>
                                        <button class="btn btn-sm btn-outline-primary" type="button">Manage Storage</button>
                                    </div>
                                    <div class="card-body">
                                        <p>Storage details and management options will be displayed here.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/dashboard.js"></script>
</body>
</html>
