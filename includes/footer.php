<?php
// Get the correct path for JavaScript files based on current directory
$root_path = $_SERVER['DOCUMENT_ROOT'] . '/AgriCool_Link/';
$current_path = dirname($_SERVER['SCRIPT_FILENAME']);
$relative_path = str_replace($root_path, '', $current_path);
$js_path = $relative_path ? '../js/' : 'js/';
?>

<footer class="footer bg-dark text-white mt-5 py-4">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4">
                <h5>AgriCool Link</h5>
                <p class="text-muted">Connecting Zambian farmers with reliable cold storage and direct market access.</p>
                <div class="d-flex mt-3">
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo $relative_path ? '../index.php' : 'index.php'; ?>" class="text-decoration-none text-muted">Home</a></li>
                    <li><a href="<?php echo $relative_path ? 'marketplace.php' : 'pages/marketplace.php'; ?>" class="text-decoration-none text-muted">Marketplace</a></li>
                    <li><a href="#" class="text-decoration-none text-muted">About Us</a></li>
                    <li><a href="#" class="text-decoration-none text-muted">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5>Contact Info</h5>
                <ul class="list-unstyled text-muted">
                    <li><i class="bi bi-geo-alt me-2"></i> Lusaka, Zambia</li>
                    <li><i class="bi bi-telephone me-2"></i> +260 97 000 0000</li>
                    <li><i class="bi bi-envelope me-2"></i> info@agricoollink.com</li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5>Newsletter</h5>
                <p class="text-muted">Subscribe for updates on market prices and agricultural tips.</p>
                <form>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your Email">
                        <button class="btn btn-success" type="submit">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
        <hr class="mt-4">
        <div class="row">
            <div class="col-md-6">
                <p class="text-muted mb-0">&copy; 2025 AgriCool Link. All rights reserved.</p>
            </div>
            <div class="col-md-6">
                <ul class="list-inline text-md-end mb-0">
                    <li class="list-inline-item"><a href="#" class="text-decoration-none text-muted">Privacy Policy</a></li>
                    <li class="list-inline-item"><a href="#" class="text-decoration-none text-muted">Terms of Use</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!-- Core JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<?php if (strpos(basename($_SERVER['PHP_SELF']), 'dashboard') !== false): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo $js_path; ?>dashboard.js"></script>
<?php endif; ?>

<?php if (basename($_SERVER['PHP_SELF']) === 'marketplace.php'): ?>
<script src="<?php echo $js_path; ?>marketplace.js"></script>
<script src="<?php echo $js_path; ?>filter-manager.js"></script>
<?php endif; ?>

<script src="<?php echo $js_path; ?>main.js"></script>

</body>
</html>
