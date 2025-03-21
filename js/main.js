// Sample product data - This would normally come from a backend API
const sampleProducts = [
    {
        id: 1,
        name: 'Fresh Tomatoes',
        farmer: 'John Mumba',
        location: 'Lusaka',
        price: 'K25/kg',
        quantity: '500 kg',
        image: 'images/tomatoes.jpg'
    },
    {
        id: 2,
        name: 'Green Vegetables',
        farmer: 'Mary Banda',
        location: 'Chipata',
        price: 'K15/kg',
        quantity: '200 kg',
        image: 'images/vegetables.jpg'
    },
    {
        id: 3,
        name: 'Sweet Potatoes',
        farmer: 'David Phiri',
        location: 'Kitwe',
        price: 'K20/kg',
        quantity: '1000 kg',
        image: 'images/sweet-potatoes.jpg'
    }
];

// DOM Elements
const featuredProductsContainer = document.getElementById('featured-products');
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');

// Load featured products
function loadFeaturedProducts() {
    if (featuredProductsContainer) {
        featuredProductsContainer.innerHTML = sampleProducts.map(product => `
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="${product.image}" class="card-img-top" alt="${product.name}" 
                         onerror="this.src='images/placeholder.jpg'">
                    <div class="card-body">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="card-text">
                            <strong>Farmer:</strong> ${product.farmer}<br>
                            <strong>Location:</strong> ${product.location}<br>
                            <strong>Price:</strong> ${product.price}<br>
                            <strong>Available:</strong> ${product.quantity}
                        </p>
                        <button class="btn btn-success btn-sm">Contact Farmer</button>
                        <button class="btn btn-outline-success btn-sm">View Details</button>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

// Handle login form submission
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        
        // TODO: Implement actual login logic
        console.log('Login attempt:', { email });
        alert('Login functionality will be implemented with backend integration');
    });
}

// Handle registration form submission
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = {
            name: document.getElementById('registerName').value,
            email: document.getElementById('registerEmail').value,
            phone: document.getElementById('registerPhone').value,
            password: document.getElementById('registerPassword').value,
            userType: document.getElementById('userType').value
        };
        
        // TODO: Implement actual registration logic
        console.log('Registration data:', formData);
        alert('Registration functionality will be implemented with backend integration');
    });
}

// Initialize tooltips and popovers
document.addEventListener('DOMContentLoaded', function() {
    loadFeaturedProducts();
    
    // Initialize Bootstrap components
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
