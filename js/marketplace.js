document.addEventListener('DOMContentLoaded', function() {
    // Product data structure
    const products = {
        vegetables: [
            { id: 'v1', name: 'Fresh Tomatoes', price: 25, unit: 'kg', image: '../images/products/veg-tomatoes.jpg' },
            { id: 'v2', name: 'Fresh Cabbage', price: 15, unit: 'head', image: '../images/products/veg-cabbage.jpg' },
            { id: 'v3', name: 'Green Peppers', price: 30, unit: 'kg', image: '../images/products/veg-peppers.jpg' },
            { id: 'v4', name: 'Carrots', price: 20, unit: 'kg', image: '../images/products/veg-carrots.jpg' }
        ],
        fruits: [
            { id: 'f1', name: 'Sweet Mangoes', price: 30, unit: 'kg', image: '../images/products/fruit-mangoes.jpg' },
            { id: 'f2', name: 'Ripe Bananas', price: 20, unit: 'dozen', image: '../images/products/fruit-bananas.jpg' },
            { id: 'f3', name: 'Fresh Oranges', price: 25, unit: 'kg', image: '../images/products/fruit-oranges.jpg' },
            { id: 'f4', name: 'Pineapples', price: 35, unit: 'piece', image: '../images/products/fruit-pineapples.jpg' }
        ],
        grains: [
            { id: 'g1', name: 'White Maize', price: 180, unit: '50kg', image: '../images/products/grain-maize.jpg' },
            { id: 'g2', name: 'Local Rice', price: 250, unit: '50kg', image: '../images/products/grain-rice.jpg' },
            { id: 'g3', name: 'Sorghum', price: 160, unit: '50kg', image: '../images/products/grain-sorghum.jpg' },
            { id: 'g4', name: 'Millet', price: 170, unit: '50kg', image: '../images/products/grain-millet.jpg' }
        ],
        tubers: [
            { id: 't1', name: 'Sweet Potatoes', price: 35, unit: 'kg', image: '../images/products/tuber-sweet-potatoes.jpg' },
            { id: 't2', name: 'Fresh Cassava', price: 25, unit: 'kg', image: '../images/products/tuber-cassava.jpg' },
            { id: 't3', name: 'Irish Potatoes', price: 40, unit: 'kg', image: '../images/products/tuber-irish-potatoes.jpg' },
            { id: 't4', name: 'Yams', price: 45, unit: 'kg', image: '../images/products/tuber-yams.jpg' }
        ],
        legumes: [
            { id: 'l1', name: 'Red Beans', price: 45, unit: 'kg', image: '../images/products/legume-beans.jpg' },
            { id: 'l2', name: 'Groundnuts', price: 40, unit: 'kg', image: '../images/products/legume-groundnuts.jpg' },
            { id: 'l3', name: 'Soybeans', price: 50, unit: 'kg', image: '../images/products/legume-soybeans.jpg' },
            { id: 'l4', name: 'Green Peas', price: 55, unit: 'kg', image: '../images/products/legume-peas.jpg' }
        ],
        herbs: [
            { id: 'h1', name: 'Fresh Chili', price: 50, unit: 'kg', image: '../images/products/herb-chili.jpg' },
            { id: 'h2', name: 'Fresh Ginger', price: 55, unit: 'kg', image: '../images/products/herb-ginger.jpg' },
            { id: 'h3', name: 'Garlic', price: 60, unit: 'kg', image: '../images/products/herb-garlic.jpg' },
            { id: 'h4', name: 'Turmeric', price: 65, unit: 'kg', image: '../images/products/herb-turmeric.jpg' }
        ]
    };

    // Cart state
    let cart = [];
    const itemsPerPage = 12;
    let currentPage = 1;
    let currentCategory = 'all';
    let currentSort = 'popular';

    // Initialize filters
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');
    const searchInput = document.getElementById('searchInput');

    // Filter event listeners
    categoryFilter.addEventListener('change', function() {
        currentCategory = this.value;
        currentPage = 1;
        updateProducts();
    });

    sortFilter.addEventListener('change', function() {
        currentSort = this.value;
        updateProducts();
    });

    searchInput.addEventListener('input', debounce(function() {
        updateProducts();
    }, 300));

    // Pagination event listeners
    document.querySelectorAll('.pagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.textContent;
            if (page === 'Previous') {
                if (currentPage > 1) currentPage--;
            } else if (page === 'Next') {
                const maxPages = Math.ceil(getAllProducts().length / itemsPerPage);
                if (currentPage < maxPages) currentPage++;
            } else {
                currentPage = parseInt(page);
            }
            updateProducts();
            updatePagination();
        });
    });

    // Get all products based on current filters
    function getAllProducts() {
        let allProducts = [];
        if (currentCategory === 'all') {
            Object.values(products).forEach(category => {
                allProducts = allProducts.concat(category);
            });
        } else {
            allProducts = products[currentCategory] || [];
        }

        // Apply search filter
        const searchTerm = searchInput.value.toLowerCase();
        if (searchTerm) {
            allProducts = allProducts.filter(product => 
                product.name.toLowerCase().includes(searchTerm)
            );
        }

        // Apply sort
        switch (currentSort) {
            case 'price-low':
                allProducts.sort((a, b) => a.price - b.price);
                break;
            case 'price-high':
                allProducts.sort((a, b) => b.price - a.price);
                break;
            case 'newest':
                // In a real app, would sort by date added
                break;
            default: // 'popular'
                // In a real app, would sort by popularity metrics
                break;
        }

        return allProducts;
    }

    // Update product display
    function updateProducts() {
        const allProducts = getAllProducts();
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageProducts = allProducts.slice(startIndex, endIndex);

        const productsGrid = document.querySelector('.row.g-4');
        productsGrid.innerHTML = pageProducts.map(product => `
            <div class="col-md-4 col-lg-3">
                <div class="card product-card">
                    <img src="${product.image}" class="card-img-top" alt="${product.name}">
                    <div class="card-body">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="card-text">Quality produce from local farmers</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">K${product.price}/${product.unit}</span>
                            <button class="btn btn-success btn-sm" onclick="addToCart('${product.id}')">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        updatePagination();
    }

    // Update pagination controls
    function updatePagination() {
        const totalProducts = getAllProducts().length;
        const totalPages = Math.ceil(totalProducts / itemsPerPage);
        const pagination = document.querySelector('.pagination');
        
        let paginationHTML = `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
        `;

        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += `
                <li class="page-item ${currentPage === i ? 'active' : ''}">
                    <a class="page-link" href="#">${i}</a>
                </li>
            `;
        }

        paginationHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#">Next</a>
            </li>
        `;

        pagination.innerHTML = paginationHTML;

        // Reattach event listeners
        document.querySelectorAll('.pagination .page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.textContent;
                if (page === 'Previous') {
                    if (currentPage > 1) currentPage--;
                } else if (page === 'Next') {
                    if (currentPage < totalPages) currentPage++;
                } else {
                    currentPage = parseInt(page);
                }
                updateProducts();
            });
        });
    }

    // Cart functions
    function addToCart(productId) {
        const product = findProduct(productId);
        if (!product) return;

        const existingItem = cart.find(item => item.id === productId);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({ ...product, quantity: 1 });
        }

        updateCartDisplay();
        showNotification(`Added ${product.name} to cart`);
    }

    function findProduct(productId) {
        for (const category of Object.values(products)) {
            const product = category.find(p => p.id === productId);
            if (product) return product;
        }
        return null;
    }

    function updateCartDisplay() {
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const cartCount = document.querySelector('.cart-count');

        if (cart.length === 0) {
            cartItems.innerHTML = '<p class="text-muted">Your cart is empty</p>';
            cartTotal.textContent = 'K0.00';
            cartCount.textContent = '0';
            return;
        }

        cartItems.innerHTML = cart.map(item => `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h6 class="mb-0">${item.name}</h6>
                    <small class="text-muted">K${item.price} × ${item.quantity}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-2">K${item.price * item.quantity}</span>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart('${item.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');

        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        cartTotal.textContent = `K${total.toFixed(2)}`;
        cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    }

    function removeFromCart(productId) {
        const index = cart.findIndex(item => item.id === productId);
        if (index !== -1) {
            const item = cart[index];
            if (item.quantity > 1) {
                item.quantity--;
            } else {
                cart.splice(index, 1);
            }
            updateCartDisplay();
            showNotification(`Removed ${item.name} from cart`);
        }
    }

    // Utility functions
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

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initialize the page
    updateProducts();
});
