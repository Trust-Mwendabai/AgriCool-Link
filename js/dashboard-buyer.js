document.addEventListener('DOMContentLoaded', function() {
    // Toggle Sidebar
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }

    // Initialize all tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize all popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Menu Items Click Handlers
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            menuItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Spending Chart
    const spendingCtx = document.getElementById('spendingChart');
    if (spendingCtx) {
        // Get monthly spending data from the data attribute
        let monthlySpendingData = {};
        try {
            monthlySpendingData = JSON.parse(spendingCtx.getAttribute('data-spending') || '{}');
        } catch (e) {
            console.error('Error parsing monthly spending data:', e);
            monthlySpendingData = {};
        }
        
        // Prepare chart data
        const months = Object.keys(monthlySpendingData).length > 0 ? 
            Object.keys(monthlySpendingData) : 
            ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            
        const spendingValues = Object.keys(monthlySpendingData).length > 0 ? 
            Object.values(monthlySpendingData) : 
            [0, 0, 0, 0, 0, 0];
        
        new Chart(spendingCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Monthly Spending (K)',
                    data: spendingValues,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'K' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'K' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Categories Chart
    const categoriesCtx = document.getElementById('categoriesChart');
    if (categoriesCtx) {
        new Chart(categoriesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Vegetables', 'Fruits', 'Tubers', 'Grains'],
                datasets: [{
                    data: [40, 25, 20, 15],
                    backgroundColor: [
                        '#198754',
                        '#ffc107',
                        '#0dcaf0',
                        '#6c757d'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Order Status Filters
    const statusFilters = document.querySelectorAll('.status-filter');
    statusFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            statusFilters.forEach(f => f.classList.remove('active'));
            this.classList.add('active');
            const status = this.getAttribute('data-status');
            filterOrders(status);
        });
    });

    // Filter Orders
    function filterOrders(status) {
        const orders = document.querySelectorAll('.order-item');
        orders.forEach(order => {
            const orderStatus = order.getAttribute('data-status');
            if (status === 'all' || orderStatus === status) {
                order.style.display = 'block';
            } else {
                order.style.display = 'none';
            }
        });
    }

    // Search Orders
    const orderSearch = document.getElementById('orderSearch');
    if (orderSearch) {
        orderSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const orders = document.querySelectorAll('.order-item');
            
            orders.forEach(order => {
                const text = order.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    order.style.display = 'block';
                } else {
                    order.style.display = 'none';
                }
            });
        });
    }

    // Add to Cart Functionality
    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            addToCart(productId, productName);
        });
    });

    function addToCart(productId, productName) {
        showNotification(`Added ${productName} to cart`);
        updateCartCount(1);
    }

    // Update Cart Count
    function updateCartCount(change) {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            const currentCount = parseInt(cartCount.textContent);
            cartCount.textContent = currentCount + change;
        }
    }

    // Order Actions
    const orderActions = document.querySelectorAll('.order-action');
    orderActions.forEach(action => {
        action.addEventListener('click', function(e) {
            e.preventDefault();
            const actionType = this.getAttribute('data-action');
            const orderId = this.getAttribute('data-order-id');
            handleOrderAction(actionType, orderId);
        });
    });

    function handleOrderAction(action, orderId) {
        switch(action) {
            case 'view':
                showNotification(`Viewing order #${orderId}`);
                break;
            case 'track':
                showNotification(`Tracking order #${orderId}`);
                break;
            case 'cancel':
                if (confirm('Are you sure you want to cancel this order?')) {
                    showNotification(`Order #${orderId} cancelled`);
                }
                break;
        }
    }

    // Supplier Rating
    const ratingStars = document.querySelectorAll('.rating-star');
    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            const supplierId = this.getAttribute('data-supplier-id');
            rateSupplier(supplierId, rating);
        });
    });

    function rateSupplier(supplierId, rating) {
        showNotification(`Rated supplier #${supplierId} with ${rating} stars`);
    }

    // Sort Orders
    const sortOrders = document.getElementById('sortOrders');
    if (sortOrders) {
        sortOrders.addEventListener('change', function() {
            const sortBy = this.value;
            showNotification(`Sorting orders by ${sortBy}`);
        });
    }

    // Export Orders
    const exportOrders = document.getElementById('exportOrders');
    if (exportOrders) {
        exportOrders.addEventListener('click', function(e) {
            e.preventDefault();
            showNotification('Exporting orders data...');
        });
    }

    // Print Order
    const printOrder = document.getElementById('printOrder');
    if (printOrder) {
        printOrder.addEventListener('click', function(e) {
            e.preventDefault();
            window.print();
        });
    }

    // Notification System
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.firstChild.classList.remove('show');
            setTimeout(() => notification.remove(), 150);
        }, 3000);
    }

    // Recommended Products Carousel
    const recommendedProducts = document.querySelector('.recommended-products');
    if (recommendedProducts) {
        const productCards = recommendedProducts.querySelectorAll('.product-card');
        let currentIndex = 0;

        function showProducts(index) {
            productCards.forEach((card, i) => {
                if (i >= index && i < index + 4) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        document.querySelector('.prev-products')?.addEventListener('click', () => {
            currentIndex = Math.max(0, currentIndex - 4);
            showProducts(currentIndex);
        });

        document.querySelector('.next-products')?.addEventListener('click', () => {
            currentIndex = Math.min(productCards.length - 4, currentIndex + 4);
            showProducts(currentIndex);
        });

        // Initialize
        showProducts(0);
    }

    // Quick Actions
    const quickActions = document.querySelectorAll('.quick-action');
    quickActions.forEach(action => {
        action.addEventListener('click', function(e) {
            e.preventDefault();
            const actionType = this.getAttribute('data-action');
            handleQuickAction(actionType);
        });
    });

    function handleQuickAction(action) {
        showNotification(`Performing quick action: ${action}`);
    }
});
