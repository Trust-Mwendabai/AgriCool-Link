<?php
// Set page title
$page_title = "AgriCool Link - Order Confirmation";

// Include header
require_once '../includes/header.php';

// Make sure user is logged in
if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

// Check if we have an order ID in the session
if (!isset($_SESSION['order_success']) || !isset($_SESSION['order_id'])) {
    header('Location: ../pages/marketplace.php');
    exit;
}

$order_id = $_SESSION['order_id'];

// Get order details from database
$sql = "SELECT o.*, u.first_name, u.last_name, u.email, u.phone 
        FROM orders o 
        JOIN users u ON o.buyer_id = u.id 
        WHERE o.id = ? AND o.buyer_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) != 1) {
    header('Location: ../pages/marketplace.php');
    exit;
}

$order = mysqli_fetch_assoc($result);

// Get order items
$items_sql = "SELECT oi.*, p.name, p.unit, p.image_url 
             FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?";

$stmt = mysqli_prepare($conn, $items_sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);
$items = [];

while ($item = mysqli_fetch_assoc($items_result)) {
    $items[] = $item;
}

// Clear the order from session now that we've loaded the data
unset($_SESSION['order_success']);
unset($_SESSION['order_id']);
?>

<div class="container mt-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="my-2">Order Confirmed!</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                        <h5 class="mt-3">Thank you for your order</h5>
                        <p class="text-muted">Order #<?php echo $order_id; ?></p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Order Details</h6>
                            <p class="mb-1">Date: <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                            <p class="mb-1">Payment Method: <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                            <p class="mb-1">Payment Status: <?php echo ucfirst($order['payment_status']); ?></p>
                            <p class="mb-1">Order Status: <?php echo ucfirst($order['status']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Shipping Information</h6>
                            <p class="mb-1"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                            <p class="mb-1"><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                            <p class="mb-1">Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                            <p class="mb-1">Email: <?php echo htmlspecialchars($order['email']); ?></p>
                        </div>
                    </div>
                    
                    <h6>Order Summary</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $item['image_url'] ?: '../images/placeholder.jpg'; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                 class="img-thumbnail me-2" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                                        </div>
                                    </td>
                                    <td>K<?php echo number_format($item['price_per_unit'], 2); ?>/<?php echo htmlspecialchars($item['unit']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td class="text-end">K<?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                                    <td class="text-end">K25.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong>K<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <?php if ($order['payment_method'] === 'mobile_money'): ?>
                    <div class="alert alert-info mt-3">
                        <h6>Mobile Money Payment Instructions</h6>
                        <p>Please complete your payment using the following details:</p>
                        <ol>
                            <li>Dial *XXX# on your mobile phone</li>
                            <li>Select "Pay Bill"</li>
                            <li>Enter Merchant Code: <strong>AGRICOOL123</strong></li>
                            <li>Enter Reference Number: <strong><?php echo $order_id; ?></strong></li>
                            <li>Enter Amount: <strong>K<?php echo number_format($order['total_amount'], 2); ?></strong></li>
                            <li>Confirm payment with your PIN</li>
                        </ol>
                    </div>
                    <?php elseif ($order['payment_method'] === 'bank_transfer'): ?>
                    <div class="alert alert-info mt-3">
                        <h6>Bank Transfer Instructions</h6>
                        <p>Please complete your payment using the following banking details:</p>
                        <p><strong>Bank:</strong> Zambia National Bank</p>
                        <p><strong>Account Name:</strong> AgriCool Link</p>
                        <p><strong>Account Number:</strong> 0123456789</p>
                        <p><strong>Reference:</strong> ORDER-<?php echo $order_id; ?></p>
                        <p><strong>Amount:</strong> K<?php echo number_format($order['total_amount'], 2); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <p>A confirmation email has been sent to <?php echo htmlspecialchars($order['email']); ?></p>
                        <div class="d-grid gap-2 d-md-block">
                            <a href="../pages/marketplace.php" class="btn btn-success">Continue Shopping</a>
                            <?php if (checkUserRole('buyer')): ?>
                            <a href="../pages/dashboard-buyer.php" class="btn btn-outline-success">My Orders</a>
                            <?php elseif (checkUserRole('farmer')): ?>
                            <a href="../pages/dashboard-farmer.php" class="btn btn-outline-success">My Dashboard</a>
                            <?php endif; ?>
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
