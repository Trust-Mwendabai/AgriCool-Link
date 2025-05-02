document.addEventListener('DOMContentLoaded', function() {
    // Cart state
    let cart = [];
    
    // Initialize cart from localStorage if available
    if (localStorage.getItem('agricoolCart')) {
        try {
            cart = JSON.parse(localStorage.getItem('agricoolCart'));
            updateCartBadge();
            updateCheckoutButton();
        } catch (e) {
            console.error('Error loading cart from localStorage:', e);
            localStorage.removeItem('agricoolCart');
            cart = [];
        }
    }
    
    // Initialize cart display
    updateCartDisplay();

    // Add event listeners to all add-to-cart buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productPrice = parseFloat(this.dataset.price);
            const productUnit = this.dataset.unit;
            const productImage = this.dataset.image;
            
            addToCart(productId, productName, productPrice, productUnit, productImage);
        });
    });
    
    // Product detail functionality
    document.querySelectorAll('.product-detail-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productPrice = parseFloat(this.dataset.price);
            const productUnit = this.dataset.unit;
            const productImage = this.dataset.image;
            const productDescription = this.dataset.description;
            const productFarmer = this.dataset.farmer;
            const productCategory = this.dataset.category;
            
            // Populate modal
            document.querySelector('.product-detail-image').src = productImage;
            document.querySelector('.product-detail-name').textContent = productName;
            document.querySelector('.product-detail-description').textContent = productDescription;
            document.querySelector('.product-detail-price').textContent = `K${productPrice}/${productUnit}`;
            document.querySelector('.product-detail-farmer').textContent = `Seller: ${productFarmer}`;
            document.querySelector('.product-detail-category').textContent = `Category: ${productCategory}`;
            
            document.querySelector('.product-detail-qty').value = 1;
            
            document.querySelector('.add-to-cart-detail-btn').dataset.id = productId;
            document.querySelector('.add-to-cart-detail-btn').dataset.name = productName;
            document.querySelector('.add-to-cart-detail-btn').dataset.price = productPrice;
            document.querySelector('.add-to-cart-detail-btn').dataset.unit = productUnit;
            document.querySelector('.add-to-cart-detail-btn').dataset.image = productImage;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('productDetailModal'));
            modal.show();
        });
    });
    
    // Product detail quantity buttons
    document.querySelector('.decrease-qty-btn').addEventListener('click', function() {
        const qtyInput = document.querySelector('.product-detail-qty');
        let qty = parseInt(qtyInput.value);
        if (qty > 1) {
            qtyInput.value = qty - 1;
        }
    });
    
    document.querySelector('.increase-qty-btn').addEventListener('click', function() {
        const qtyInput = document.querySelector('.product-detail-qty');
        let qty = parseInt(qtyInput.value);
        qtyInput.value = qty + 1;
    });
    
    // Add to cart from detail modal
    document.querySelector('.add-to-cart-detail-btn').addEventListener('click', function() {
        const productId = this.dataset.id;
        const productName = this.dataset.name;
        const productPrice = parseFloat(this.dataset.price);
        const productUnit = this.dataset.unit;
        const productImage = this.dataset.image;
        const quantity = parseInt(document.querySelector('.product-detail-qty').value);
        
        addToCart(productId, productName, productPrice, productUnit, productImage, quantity);
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('productDetailModal'));
        modal.hide();
    });
    
    // Cart functions
    function addToCart(productId, productName, productPrice, productUnit, productImage, quantity = 1) {
        // Check if product is already in cart
        const existingItem = cart.find(item => item.id === productId);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: productId,
                name: productName,
                price: productPrice,
                unit: productUnit,
                image: productImage,
                quantity: quantity
            });
        }

        // Save to localStorage
        localStorage.setItem('agricoolCart', JSON.stringify(cart));
        
        updateCartDisplay();
        updateCartBadge();
        updateCheckoutButton();
        showNotification(`${productName} added to cart`);
    }

    function findProduct(productId) {
        for (const category of Object.values(products)) {
            const product = category.find(p => p.id === productId);
            if (product) return product;
        }
        return null;
    }

    function updateCartBadge() {
        const cartCountElements = document.querySelectorAll('.cart-count');
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        
        cartCountElements.forEach(element => {
            element.textContent = totalItems;
            element.style.display = totalItems > 0 ? 'inline' : 'none';
        });
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
