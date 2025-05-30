<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage Provider Dashboard - AgriCool Link</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
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
                    <a href="#" class="menu-item">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-item">
                        <i class="bi bi-thermometer-snow"></i>
                        <span>Storage Units</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-item">
                        <i class="bi bi-battery-charging"></i>
                        <span>Power Status</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-item">
                        <i class="bi bi-people"></i>
                        <span>Clients</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-item">
                        <i class="bi bi-calendar-check"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-item">
                        <i class="bi bi-graph-up"></i>
                        <span>Analytics</span>
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
                                    4
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <h6 class="dropdown-header">Alerts</h6>
                                <a class="dropdown-item text-danger" href="#">Temperature alert - Unit A</a>
                                <a class="dropdown-item" href="#">New storage request</a>
                                <a class="dropdown-item" href="#">Power backup activated</a>
                                <a class="dropdown-item" href="#">Maintenance due</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                <img src="../images/placeholder.jpg" class="rounded-circle me-2" width="32" height="32">
                                <span>Cold Storage Co.</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#"><i class="bi bi-building me-2"></i>Company Profile</a>
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
                <!-- Overview Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="stats-icon bg-primary">
                            <i class="bi bi-thermometer-snow text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Storage Units</h6>
                        <h2 class="card-title mb-0">8</h2>
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
                            <i class="bi bi-battery-full text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Power Status</h6>
                        <h2 class="card-title mb-0">Optimal</h2>
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
                            <i class="bi bi-people text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Active Clients</h6>
                        <h2 class="card-title mb-0">24</h2>
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
                        <div class="stats-icon bg-info">
                            <i class="bi bi-percent text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Capacity Used</h6>
                        <h2 class="card-title mb-0">78%</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Storage Units Status -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Storage Units Status</h5>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary active">Temperature</button>
                                    <button class="btn btn-sm btn-outline-primary">Humidity</button>
                                    <button class="btn btn-sm btn-outline-primary">Power</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="storageChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Power Consumption</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="powerChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Storage Units Grid -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Storage Units Overview</h5>
                        <button class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-2"></i>Add Unit
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-3">
                                <div class="storage-unit-card">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title">Unit A</h5>
                                                <span class="badge bg-success">Active</span>
                                            </div>
                                            <div class="storage-stats mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Temperature</span>
                                                    <span class="text-success">4°C</span>
                                                </div>
                                                <div class="progress mb-3" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: 75%"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Humidity</span>
                                                    <span>85%</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-info" style="width: 85%"></div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">12 Active Items</small>
                                                <button class="btn btn-sm btn-outline-primary">Details</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- More storage units... -->
                            <div class="col-md-6 col-lg-3">
                                <div class="storage-unit-card">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title">Unit B</h5>
                                                <span class="badge bg-warning">Warning</span>
                                            </div>
                                            <div class="storage-stats mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Temperature</span>
                                                    <span class="text-warning">6°C</span>
                                                </div>
                                                <div class="progress mb-3" style="height: 6px;">
                                                    <div class="progress-bar bg-warning" style="width: 90%"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Humidity</span>
                                                    <span>80%</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-info" style="width: 80%"></div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">8 Active Items</small>
                                                <button class="btn btn-sm btn-outline-primary">Details</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="storage-unit-card">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title">Unit C</h5>
                                                <span class="badge bg-success">Active</span>
                                            </div>
                                            <div class="storage-stats mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Temperature</span>
                                                    <span class="text-success">3°C</span>
                                                </div>
                                                <div class="progress mb-3" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: 45%"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Humidity</span>
                                                    <span>82%</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-info" style="width: 82%"></div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">15 Active Items</small>
                                                <button class="btn btn-sm btn-outline-primary">Details</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="storage-unit-card">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-3">
                                                <h5 class="card-title">Unit D</h5>
                                                <span class="badge bg-danger">Maintenance</span>
                                            </div>
                                            <div class="storage-stats mb-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Temperature</span>
                                                    <span class="text-danger">8°C</span>
                                                </div>
                                                <div class="progress mb-3" style="height: 6px;">
                                                    <div class="progress-bar bg-danger" style="width: 20%"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Humidity</span>
                                                    <span>70%</span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-info" style="width: 70%"></div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Under Maintenance</small>
                                                <button class="btn btn-sm btn-outline-primary">Details</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities and Power Backup Status -->
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Recent Activities</h5>
                                <button class="btn btn-sm btn-outline-primary">View All</button>
                            </div>
                            <div class="card-body">
                                <div class="activity-timeline">
                                    <div class="activity-item d-flex">
                                        <div class="activity-content">
                                            <small class="text-muted">2 minutes ago</small>
                                            <h6 class="mb-1">Temperature alert in Unit B</h6>
                                            <p class="mb-0">Temperature increased to 6°C. Check cooling system.</p>
                                        </div>
                                    </div>
                                    <div class="activity-item d-flex">
                                        <div class="activity-content">
                                            <small class="text-muted">15 minutes ago</small>
                                            <h6 class="mb-1">New storage request</h6>
                                            <p class="mb-0">Farmer John requested storage space for 500kg of tomatoes.</p>
                                        </div>
                                    </div>
                                    <div class="activity-item d-flex">
                                        <div class="activity-content">
                                            <small class="text-muted">1 hour ago</small>
                                            <h6 class="mb-1">Power backup activated</h6>
                                            <p class="mb-0">Main power supply interrupted. Backup generator active.</p>
                                        </div>
                                    </div>
                                    <div class="activity-item d-flex">
                                        <div class="activity-content">
                                            <small class="text-muted">2 hours ago</small>
                                            <h6 class="mb-1">Maintenance completed</h6>
                                            <p class="mb-0">Unit D maintenance check completed successfully.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Power Backup Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="backup-status mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Main Power Supply</h6>
                                        <span class="badge bg-success">Active</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: 100%"></div>
                                    </div>
                                </div>
                                <div class="backup-status mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Generator Status</h6>
                                        <span class="badge bg-success">Standby</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 85%"></div>
                                    </div>
                                    <small class="text-muted">Fuel Level: 85%</small>
                                </div>
                                <div class="backup-status">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Solar Backup</h6>
                                        <span class="badge bg-success">Charging</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-info" style="width: 60%"></div>
                                    </div>
                                    <small class="text-muted">Battery Level: 60%</small>
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
    <script src="../js/dashboard-storage.js"></script>
</body>
</html>
