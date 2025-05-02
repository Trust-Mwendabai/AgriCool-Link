<?php
// Set page title
$page_title = "AgriCool Link - Checkout";

// Include header
require_once '../includes/header.php';

// Make sure user is logged in
if (!isLoggedIn()) {
    // Redirect to login page with message
    $_SESSION['login_error'] = 'Please log in to checkout';
    header('Location: ../index.php');
    exit;
}

// Make sure user is a buyer (or allow farmer to also buy)
if (!checkUserRole(['buyer', 'farmer'])) {
    header('Location: ../index.php');
    exit;
}

// Get current user information
$user = getCurrentUser();
?>

<div class="container mt-5 pt-4">
    <h1 class="mb-4">Checkout</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <form id="checkoutForm" action="../includes/process_order.php" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?php echo htmlspecialchars($user['location'] ?? ''); ?></textarea>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="mobile_money" value="mobile_money" checked>
                                <label class="form-check-label" for="mobile_money">
                                    Mobile Money
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cash_on_delivery" value="cash_on_delivery">
                                <label class="form-check-label" for="cash_on_delivery">
                                    Cash on Delivery
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                                <label class="form-check-label" for="bank_transfer">
                                    Bank Transfer
                                </label>
                            </div>
                        </div>
                        
                        <div id="mobile_money_details" class="mb-3">
                            <label for="mobile_number" class="form-label">Mobile Money Number</label>
                            <input type="tel" class="form-control" id="mobile_number" name="mobile_number" placeholder="Enter your mobile money number">
                        </div>
                        
                        <div id="bank_transfer_details" class="mb-3 d-none">
                            <p class="mb-1">Bank: Zambia National Bank</p>
                            <p class="mb-1">Account Name: AgriCool Link</p>
                            <p class="mb-1">Account Number: 0123456789</p>
                            <p class="mb-3">Reference: Your order number will be provided after placing the order</p>
                            <label for="transaction_id" class="form-label">Transaction ID (after payment)</label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="Enter the transaction ID after making payment">
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="placeOrderBtn">Place Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div id="cart-summary">
                        <!-- Cart summary will be loaded here via JavaScript -->
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">K0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span id="shipping">K25.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Total:</strong>
                        <strong id="total-amount">K0.00</strong>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>If you have any questions or concerns about your order, please contact us:</p>
                    <p><i class="bi bi-telephone me-2"></i> +260 97 000 0000</p>
                    <p><i class="bi bi-envelope me-2"></i> orders@agricoollink.com</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get cart from localStorage
    let cart = [];
    if (localStorage.getItem('agricoolCart')) {
        try {
            cart = JSON.parse(localStorage.getItem('agricoolCart'));
            
            // Update cart summary
            const cartSummary = document.getElementById('cart-summary');
            const subtotalElement = document.getElementById('subtotal');
            const totalElement = document.getElementById('total-amount');
            
            if (cart.length === 0) {
                cartSummary.innerHTML = '<p class="text-muted">Your cart is empty</p>';
                document.getElementById('placeOrderBtn').disabled = true;
                return;
            }
            
            cartSummary.innerHTML = cart.map(item => `
                <div class="mb-3">
                    <div class="d-flex align-items-center">
                        <img src="${item.image}" alt="${item.name}" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0">${item.name}</h6>
                            <small class="text-muted">K${item.price} × ${item.quantity} ${item.unit}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span>K${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                </div>
            `).join('');
            
            // Calculate subtotal
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            subtotalElement.textContent = `K${subtotal.toFixed(2)}`;
            
            // Calculate total (subtotal + shipping)
            const shipping = 25; // Fixed shipping cost
            const total = subtotal + shipping;
            totalElement.textContent = `K${total.toFixed(2)}`;
            
            // Add cart items to form as hidden inputs
            const form = document.getElementById('checkoutForm');
            form.innerHTML += `<input type="hidden" name="cart_items" value='${JSON.stringify(cart)}'>`;
            form.innerHTML += `<input type="hidden" name="subtotal" value="${subtotal}">`;
            form.innerHTML += `<input type="hidden" name="shipping" value="${shipping}">`;
            form.innerHTML += `<input type="hidden" name="total" value="${total}">`;
        } catch (e) {
            console.error('Error parsing cart from localStorage:', e);
        }
    } else {
        document.getElementById('cart-summary').innerHTML = '<p class="text-muted">Your cart is empty</p>';
        document.getElementById('placeOrderBtn').disabled = true;
    }
    
    // Toggle payment method details
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const mobileMoneyDetails = document.getElementById('mobile_money_details');
    const bankTransferDetails = document.getElementById('bank_transfer_details');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'mobile_money') {
                mobileMoneyDetails.classList.remove('d-none');
                bankTransferDetails.classList.add('d-none');
            } else if (this.value === 'bank_transfer') {
                mobileMoneyDetails.classList.add('d-none');
                bankTransferDetails.classList.remove('d-none');
            } else {
                mobileMoneyDetails.classList.add('d-none');
                bankTransferDetails.classList.add('d-none');
            }
        });
    });
    
    // Handle form submission
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        // Additional validation could be added here
        
        // Clear cart after successful form submission
        localStorage.removeItem('agricoolCart');
    });
});
</script>

<?php
// Include footer
require_once '../includes/footer.php';
?>
