<?php
// Set page title
$page_title = "AgriCool Link - Marketplace";

// Include header
require_once '../includes/header.php';

// Include product functions and advanced filters
require_once '../includes/product_functions.php';
require_once '../includes/advanced_filters.php';
require_once '../includes/advanced_filters_extension.php';

// Get filter parameters
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$farmer_id = isset($_GET['farmer']) ? (int)$_GET['farmer'] : null;
$price_range = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$storage_status = isset($_GET['storage_status']) ? $_GET['storage_status'] : 'any';
$freshness = isset($_GET['freshness']) ? $_GET['freshness'] : 'any';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;

// Get locations and farmers for filters
$locations = getDistinctLocations();
$farmers = getAllFarmers();
$price_ranges = getPriceRanges();
$storage_options = getColdStorageOptions();
$freshness_options = getFreshnessOptions();

// Build filters array
$filters = [];
if ($category_id) {
    $filters['category_id'] = $category_id;
}
if ($search) {
    $filters['search'] = $search;
}
if ($location) {
    $filters['location'] = $location;
}
if ($farmer_id) {
    $filters['farmer_id'] = $farmer_id;
}

// Add price range filtering
if ($price_range && strpos($price_range, '-') !== false) {
    list($min_price, $max_price) = explode('-', $price_range);
    $filters['min_price'] = (float)$min_price;
    $filters['max_price'] = (float)$max_price;
}

// Add cold storage filtering
if ($storage_status == 'cold_storage') {
    $filters['storage_status'] = 'cold_storage';
} elseif ($storage_status == 'power_backup') {
    $filters['storage_status'] = 'power_backup';
} elseif ($storage_status == 'no_storage') {
    $filters['storage_status'] = 'no_storage';
}

// Add freshness filtering
if ($freshness != 'any') {
    $filters['freshness'] = (int)$freshness;
}

$filters['sort'] = $sort;
$filters['status'] = 'available'; // Only show available products

// Calculate offset for pagination
$offset = ($page - 1) * $per_page;

// Get products and categories
$products = getProducts($filters, $per_page, $offset);
$categories = getCategories();
$total_products = countProducts($filters);
$total_pages = ceil($total_products / $per_page);
?>

<!-- Main Content -->
<div class="container mt-5 pt-4">
    <!-- Improved Top Navigation -->
    <div class="marketplace-top-nav py-3 mb-4 bg-light rounded shadow-sm">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h2 class="fw-bold h4 mb-0 me-3">AgriCool Marketplace</h2>
                    <nav aria-label="breadcrumb" class="mb-0">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../index.php" class="text-success"><i class="bi bi-house-fill me-1"></i>Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Marketplace</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <!-- Search Bar -->
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Quick search..." id="quickSearch" value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-success" type="button" id="quickSearchBtn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <!-- Filter Button -->
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-funnel-fill me-1"></i>Filters
                        <?php 
                        $filter_count = 0;
                        if ($category_id) $filter_count++;
                        if ($location) $filter_count++;
                        if ($farmer_id) $filter_count++;
                        if ($price_range) $filter_count++;
                        if ($storage_status != 'any') $filter_count++;
                        if ($freshness != 'any') $filter_count++;
                        if ($filter_count > 0):
                        ?>
                        <span class="badge bg-success"><?php echo $filter_count; ?></span>
                        <?php endif; ?>
                    </button>
                </div>
            </div>
            <p class="text-muted mb-0 mt-1">Connect directly with farmers and eliminate middlemen</p>
            
            <!-- Category Chips/Badges for Quick Filtering -->
            <div class="category-chips mt-3 pt-2 border-top">
                <div class="d-flex align-items-center overflow-auto pb-2">
                    <span class="text-muted me-2 fw-bold">Categories:</span>
                    <a href="marketplace.php" class="category-chip <?php echo (!$category_id) ? 'active' : ''; ?>">
                        <i class="bi bi-grid-fill me-1"></i>All
                    </a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="marketplace.php?category=<?php echo $cat['id']; ?><?php echo ($search ? '&search='.urlencode($search) : ''); ?><?php echo ($location ? '&location='.urlencode($location) : ''); ?><?php echo ($farmer_id ? '&farmer='.$farmer_id : ''); ?><?php echo ($storage_status != 'any' ? '&storage_status='.$storage_status : ''); ?><?php echo ($freshness != 'any' ? '&freshness='.$freshness : ''); ?><?php echo ($price_range ? '&price_range='.urlencode($price_range) : ''); ?><?php echo ($sort != 'popular' ? '&sort='.$sort : ''); ?>" 
                       class="category-chip <?php echo ($category_id == $cat['id']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="filterModalLabel"><i class="bi bi-funnel-fill me-2 text-success"></i>Filter Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="marketplace.php" method="get" id="filterForm" class="row g-3">
                        <!-- Search and Category (First Row) -->
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search Products</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="search" placeholder="Search products..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success"><i class="bi bi-grid-3x3-gap-fill"></i></span>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Location and Farmer (Second Row) -->
                        <div class="col-md-6">
                            <label for="location" class="form-label">Location</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success"><i class="bi bi-geo-alt-fill"></i></span>
                                <select class="form-select" id="location" name="location">
                                    <option value="">All Locations</option>
                                    <?php foreach ($locations as $loc): ?>
                                    <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo ($location == $loc) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($loc); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="farmer" class="form-label">Farmer</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-success"><i class="bi bi-person-fill"></i></span>
                                <select class="form-select" id="farmer" name="farmer">
                                    <option value="">All Farmers</option>
                                    <?php foreach ($farmers as $farmer): ?>
                                    <option value="<?php echo $farmer['id']; ?>" <?php echo ($farmer_id == $farmer['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($farmer['farmer_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Additional Filters (Third Row) -->
                        <div class="col-12">
                            <div class="accordion" id="advancedFiltersAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingAdvanced">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdvanced" aria-expanded="false" aria-controls="collapseAdvanced">
                                            <i class="bi bi-sliders me-2"></i> Advanced Filters
                                        </button>
                                    </h2>
                                    <div id="collapseAdvanced" class="accordion-collapse collapse" aria-labelledby="headingAdvanced" data-bs-parent="#advancedFiltersAccordion">
                                        <div class="accordion-body">
                                            <div class="row g-3">
                                                <!-- Price Range and Sort (First Row of Advanced) -->
                                                <div class="col-md-6">
                                                    <label for="price_range" class="form-label">Price Range</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light text-success"><i class="bi bi-currency-dollar"></i></span>
                                                        <select class="form-select" id="price_range" name="price_range">
                                                            <option value="">Any Price</option>
                                                            <?php foreach ($price_ranges as $range): ?>
                                                            <option value="<?php echo $range['min'] . '-' . $range['max']; ?>" <?php echo ($price_range == $range['min'] . '-' . $range['max']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($range['label']); ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="sort" class="form-label">Sort By</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light text-success"><i class="bi bi-sort-down"></i></span>
                                                        <select class="form-select" id="sort" name="sort">
                                                            <option value="popular" <?php echo ($sort == 'popular') ? 'selected' : ''; ?>>Most Popular</option>
                                                            <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Newest</option>
                                                            <option value="price-low" <?php echo ($sort == 'price-low') ? 'selected' : ''; ?>>Price: Low to High</option>
                                                            <option value="price-high" <?php echo ($sort == 'price-high') ? 'selected' : ''; ?>>Price: High to Low</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <!-- Cold Storage and Freshness (Second Row of Advanced) -->
                                                <div class="col-md-6">
                                                    <label for="storage_status" class="form-label">Cold Storage Status</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light text-info"><i class="bi bi-thermometer-snow"></i></span>
                                                        <select class="form-select" id="storage_status" name="storage_status">
                                                            <?php foreach ($storage_options as $option): ?>
                                                            <option value="<?php echo $option['value']; ?>" <?php echo ($storage_status == $option['value']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($option['label']); ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="freshness" class="form-label">Product Freshness</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light text-success"><i class="bi bi-calendar-check"></i></span>
                                                        <select class="form-select" id="freshness" name="freshness">
                                                            <?php foreach ($freshness_options as $option): ?>
                                                            <option value="<?php echo $option['value']; ?>" <?php echo ($freshness == $option['value']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($option['label']); ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-body border-top mt-3">
                    <!-- Filter History and Saved Filters -->
                    <ul class="nav nav-tabs" id="filterTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                                <i class="bi bi-clock-history me-1"></i>Recent Filters
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="saved-tab" data-bs-toggle="tab" data-bs-target="#saved" type="button" role="tab">
                                <i class="bi bi-bookmark me-1"></i>Saved Filters
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content py-3" id="filterTabContent">
                        <!-- Recent Filter History -->
                        <div class="tab-pane fade show active" id="history" role="tabpanel">
                            <div id="filterHistory" class="filter-history">
                                <!-- Filter history will be populated by JavaScript -->
                                <p class="text-muted">Loading recent filters...</p>
                            </div>
                        </div>
                        <!-- Saved Filters -->
                        <div class="tab-pane fade" id="saved" role="tabpanel">
                            <div id="savedFilters" class="saved-filters">
                                <!-- Saved filters will be populated by JavaScript -->
                                <p class="text-muted">Loading saved filters...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <a href="marketplace.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear All
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Close
                    </button>
                    <button type="submit" form="filterForm" class="btn btn-success">
                        <i class="bi bi-filter-square-fill me-1"></i>Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if ($search || $category_id || $location || $farmer_id || $price_range || $storage_status != 'any' || $freshness != 'any'): ?>
    <div class="mb-3">
        <p>
            <?php if ($search): ?>
                <span class="badge bg-success">Search: <?php echo htmlspecialchars($search); ?></span>
            <?php endif; ?>
            <?php if ($category_id): 
                $category_name = '';
                foreach ($categories as $cat) {
                    if ($cat['id'] == $category_id) {
                        $category_name = $cat['name'];
                        break;
                    }
                }
            ?>
                <span class="badge bg-primary">Category: <?php echo htmlspecialchars($category_name); ?></span>
            <?php endif; ?>
            <?php if ($location): ?>
                <span class="badge bg-info">Location: <?php echo htmlspecialchars($location); ?></span>
            <?php endif; ?>
            <?php if ($farmer_id): 
                $farmer_name = '';
                foreach ($farmers as $farmer) {
                    if ($farmer['id'] == $farmer_id) {
                        $farmer_name = $farmer['farmer_name'] . ' - ' . $farmer['farm_name'];
                        break;
                    }
                }
            ?>
                <span class="badge bg-warning text-dark">Farmer: <?php echo htmlspecialchars($farmer_name); ?></span>
            <?php endif; ?>
            <?php if ($price_range): ?>
                <span class="badge bg-secondary">Price: <?php echo htmlspecialchars($price_range); ?></span>
            <?php endif; ?>
            <?php if ($storage_status != 'any'): 
                $storage_label = '';
                foreach ($storage_options as $option) {
                    if ($option['value'] == $storage_status) {
                        $storage_label = $option['label'];
                        break;
                    }
                }
            ?>
                <span class="badge bg-info">Storage: <?php echo htmlspecialchars($storage_label); ?></span>
            <?php endif; ?>
            <?php if ($freshness != 'any'): 
                $freshness_label = '';
                foreach ($freshness_options as $option) {
                    if ($option['value'] == $freshness) {
                        $freshness_label = $option['label'];
                        break;
                    }
                }
            ?>
                <span class="badge bg-success">Freshness: <?php echo htmlspecialchars($freshness_label); ?></span>
            <?php endif; ?>
            <a href="marketplace.php" class="btn btn-sm btn-outline-secondary ms-2"><i class="bi bi-x-circle me-1"></i>Clear all filters</a>
        </p>
    </div>
    <?php endif; ?>

    <!-- Products Grid -->
    <div class="row g-4">
        <?php if (empty($products)): ?>
        <div class="col-12 text-center py-5">
            <div class="alert alert-info">
                <i class="bi bi-exclamation-circle me-2"></i>
                No products found. Try different search terms or filters.
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
            <div class="col-md-4 col-lg-3">
                <div class="card product-card h-100 shadow-sm hover-shadow">
                    <div class="product-badge">
                        <?php if ($product['created_at'] >= date('Y-m-d', strtotime('-7 days'))): ?>
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">New</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-img-container">
                        <img src="<?php echo !empty($product['image_url']) ? $product['image_url'] : '../images/placeholder.jpg'; ?>" 
                            class="card-img-top product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <span class="badge bg-<?php echo getRandomBadgeColor(); ?>"><?php echo htmlspecialchars($product['category_name']); ?></span>
                        </div>
                        <p class="card-text flex-grow-1"><?php echo htmlspecialchars(substr($product['description'], 0, 100) . (strlen($product['description']) > 100 ? '...' : '')); ?></p>
                        <div class="d-flex align-items-center mb-2">
                            <div class="d-flex align-items-center me-3">
                                <i class="bi bi-geo-alt-fill text-success me-1"></i>
                                <small><?php echo htmlspecialchars($product['farmer_location'] ?? 'Zambia'); ?></small>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-fill text-success me-1"></i>
                                <small><?php echo htmlspecialchars($product['farmer_name']); ?></small>
                            </div>
                        </div>
                        <div class="product-footer mt-auto pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h5 text-success mb-0">K<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></span>
                                    <small class="text-muted">per <?php echo htmlspecialchars($product['unit']); ?></small>
                                </div>
                                <button class="btn btn-success add-to-cart-btn" 
                                        data-id="<?php echo $product['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                        data-price="<?php echo $product['price']; ?>"
                                        data-unit="<?php echo htmlspecialchars($product['unit']); ?>"
                                        data-image="<?php echo !empty($product['image_url']) ? $product['image_url'] : '../images/placeholder.jpg'; ?>">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav class="my-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <p class="mb-0 text-muted">Showing <?php echo ($page - 1) * $per_page + 1; ?> to <?php echo min($page * $per_page, $total_products); ?> of <?php echo $total_products; ?> products</p>
            <div class="form-inline">
                <label class="me-2">Go to page:</label>
                <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <option value="marketplace.php?page=<?php echo $i; ?><?php echo ($category_id ? '&category='.$category_id : ''); ?><?php echo ($search ? '&search='.urlencode($search) : ''); ?><?php echo ($sort ? '&sort='.$sort : ''); ?>" <?php echo ($page == $i) ? 'selected' : ''; ?>>
                        <?php echo $i; ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        
        <ul class="pagination pagination-lg justify-content-center">
            <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="marketplace.php?page=1<?php echo ($category_id ? '&category='.$category_id : ''); ?><?php echo ($search ? '&search='.urlencode($search) : ''); ?><?php echo ($sort ? '&sort='.$sort : ''); ?>">
                    <i class="bi bi-chevron-double-left"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="marketplace.php?page=<?php echo ($page - 1); ?><?php echo ($category_id ? '&category='.$category_id : ''); ?><?php echo ($search ? '&search='.urlencode($search) : ''); ?><?php echo ($sort ? '&sort='.$sort : ''); ?>">
                    <i class="bi bi-chevron-left"></i> Prev
                </a>
            </li>
            <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-double-left"></i></span>
            </li>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-left"></i> Prev</span>
            </li>
            <?php endif; ?>
            
            <?php 
            $window_size = 5; // Show 5 page links at a time
            $start_page = max(1, min($page - floor($window_size/2), $total_pages - $window_size + 1));
            $end_page = min($total_pages, $start_page + $window_size - 1);
            
            // Adjust start_page if we're at the end of the page list
            if ($end_page - $start_page + 1 < $window_size) {
                $start_page = max(1, $end_page - $window_size + 1);
            }
            
            for ($i = $start_page; $i <= $end_page; $i++): 
            ?>
            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="marketplace.php?page=<?php echo $i; ?><?php echo ($category_id ? '&category='.$category_id : ''); ?><?php echo ($search ? '&search='.urlencode($search) : ''); ?><?php echo ($sort ? '&sort='.$sort : ''); ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="marketplace.php?page=<?php echo ($page + 1); ?><?php echo ($category_id ? '&category='.$category_id : ''); ?><?php echo ($search ? '&search='.urlencode($search) : ''); ?><?php echo ($sort ? '&sort='.$sort : ''); ?>">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="marketplace.php?page=<?php echo $total_pages; ?><?php echo ($category_id ? '&category='.$category_id : ''); ?><?php echo ($search ? '&search='.urlencode($search) : ''); ?><?php echo ($sort ? '&sort='.$sort : ''); ?>">
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            </li>
            <?php else: ?>
            <li class="page-item disabled">
                <span class="page-link">Next <i class="bi bi-chevron-right"></i></span>
            </li>
            <li class="page-item disabled">
                <span class="page-link"><i class="bi bi-chevron-double-right"></i></span>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
    
    <!-- Marketplace Features -->
    <div class="row mt-5 mb-4">
        <div class="col-12">
            <h2 class="text-center mb-4">Why Buy from AgriCool Link?</h2>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="feature-icon bg-success bg-opacity-10 rounded-circle text-success mb-3 mx-auto">
                        <i class="bi bi-people-fill fs-1"></i>
                    </div>
                    <h5>Cut Out Middlemen</h5>
                    <p>Buy directly from farmers and help them receive fair prices for their products. No intermediaries means better prices for everyone.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="feature-icon bg-info bg-opacity-10 rounded-circle text-info mb-3 mx-auto">
                        <i class="bi bi-thermometer-snow fs-1"></i>
                    </div>
                    <h5>Cold Storage Guaranteed</h5>
                    <p>Our products are properly stored in modern cold storage facilities to ensure freshness and reduce post-harvest losses.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="feature-icon bg-warning bg-opacity-10 rounded-circle text-warning mb-3 mx-auto">
                        <i class="bi bi-lightning-charge-fill fs-1"></i>
                    </div>
                    <h5>Power-Backup Reliability</h5>
                    <p>Our storage facilities have reliable power backup solutions to address rural electricity challenges.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cart Modal -->
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
                        <a href="marketplace.php" class="btn btn-success mt-2">
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
                    <a href="checkout.php" class="btn btn-success" id="checkout-btn" disabled>Proceed to Checkout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Detail Modal -->
<div class="modal fade" id="productDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img src="" alt="Product Image" class="img-fluid product-detail-image">
                    </div>
                    <div class="col-md-6">
                        <h3 class="product-detail-name"></h3>
                        <p class="product-detail-description"></p>
                        <p class="product-detail-price"></p>
                        <p class="product-detail-farmer"></p>
                        <p class="product-detail-category"></p>
                        <div class="d-flex align-items-center mt-3">
                            <div class="input-group w-50 me-3">
                                <button class="btn btn-outline-secondary decrease-qty-btn" type="button">-</button>
                                <input type="number" class="form-control text-center product-detail-qty" value="1" min="1">
                                <button class="btn btn-outline-secondary increase-qty-btn" type="button">+</button>
                            </div>
                            <button class="btn btn-success add-to-cart-detail-btn">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once '../includes/footer.php';
?>
