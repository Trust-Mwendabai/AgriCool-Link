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

    // Add Product Button
    const addProductBtn = document.querySelector('.btn-add-product');
    if (addProductBtn) {
        addProductBtn.addEventListener('click', () => {
            // Show add product modal
            const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
            modal.show();
        });
    }

    // Add Storage Button
    const addStorageBtn = document.querySelector('.btn-add-storage');
    if (addStorageBtn) {
        addStorageBtn.addEventListener('click', () => {
            // Show add storage modal
            const modal = new bootstrap.Modal(document.getElementById('addStorageModal'));
            modal.show();
        });
    }

    // Notification Dropdown
    const notificationDropdown = document.querySelector('.dropdown-notifications');
    if (notificationDropdown) {
        notificationDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            // Toggle notification dropdown
            const dropdown = new bootstrap.Dropdown(this);
            dropdown.toggle();
        });
    }

    // Profile Dropdown
    const profileDropdown = document.querySelector('.dropdown-profile');
    if (profileDropdown) {
        profileDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            // Toggle profile dropdown
            const dropdown = new bootstrap.Dropdown(this);
            dropdown.toggle();
        });
    }

    // Tab Navigation
    const tabLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
    tabLinks.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            const parent = target.parentElement;
            
            // Hide all tabs
            parent.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show selected tab
            target.classList.add('show', 'active');
            
            // Update active state of tab links
            tabLinks.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Form Submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // Show success message
            showNotification('Form submitted successfully!');
        });
    });

    // Action Buttons (Edit, Delete, etc.)
    const actionButtons = document.querySelectorAll('.btn-action');
    actionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.getAttribute('data-action');
            const itemId = this.getAttribute('data-id');
            
            switch(action) {
                case 'edit':
                    showNotification('Edit mode activated');
                    break;
                case 'delete':
                    if (confirm('Are you sure you want to delete this item?')) {
                        showNotification('Item deleted successfully');
                    }
                    break;
                case 'view':
                    showNotification('Loading details...');
                    break;
            }
        });
    });

    // Filter Buttons
    const filterButtons = document.querySelectorAll('.btn-filter');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            showNotification('Filter applied: ' + this.textContent);
        });
    });

    // Search Functionality
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Implement search functionality
            console.log('Searching for:', this.value);
        });
    });

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

    // Initialize Charts
    if (typeof Chart !== 'undefined') {
        // Sales Overview Chart
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Sales',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: '#198754',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        // Products Distribution Chart
        const productsCtx = document.getElementById('productsChart');
        if (productsCtx) {
            new Chart(productsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Vegetables', 'Fruits', 'Tubers', 'Grains'],
                    datasets: [{
                        data: [12, 19, 3, 5],
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
    }

    // Table Row Click Handlers
    const tableRows = document.querySelectorAll('tbody tr[data-href]');
    tableRows.forEach(row => {
        row.addEventListener('click', function() {
            window.location.href = this.getAttribute('data-href');
        });
        row.style.cursor = 'pointer';
    });

    // Export Data Buttons
    const exportButtons = document.querySelectorAll('.btn-export');
    exportButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const format = this.getAttribute('data-format');
            showNotification(`Exporting data as ${format}...`);
        });
    });

    // Print Button
    const printButtons = document.querySelectorAll('.btn-print');
    printButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            window.print();
        });
    });

    // Refresh Data Button
    const refreshButtons = document.querySelectorAll('.btn-refresh');
    refreshButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            showNotification('Refreshing data...');
            // Add your refresh logic here
        });
    });

    // Settings Toggle
    const settingsToggles = document.querySelectorAll('.settings-toggle');
    settingsToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const setting = this.getAttribute('data-setting');
            const value = this.checked;
            showNotification(`${setting} has been ${value ? 'enabled' : 'disabled'}`);
        });
    });
});
