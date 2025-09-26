<footer class="footer mt-auto py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Swift Ride</h5>
                <p class="mb-0">Your trusted partner for all your vehicle rental needs. Quality vehicles and exceptional service since 2025.</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
    <h5>Quick Links</h5>
    <ul class="list-unstyled">
        <li><a href="cars.php" class="text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Available Cars</a></li>
        <li><a href="about.php" class="text-decoration-none"><i class="fas fa-chevron-right me-2"></i>About Us</a></li>
        <li><a href="contact.php" class="text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Contact Us</a></li>

        <?php if (!function_exists('is_user_logged_in') || !is_user_logged_in()): ?>
            <li><a href="login.php" class="text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Login</a></li>
            <li><a href="register.php" class="text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Register</a></li>
        <?php endif; ?>
    </ul>
</div>

            <div class="col-md-4">
                <h5>Contact Info</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Thirunelvely Jaffna</li>
                    <li class="mb-2"><i class="fas fa-phone me-2"></i>+94 752216762</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i>Luxury@vehiclerentalsystem.com</li>
                </ul>
                <div class="social-links">
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        <hr class="mt-4 mb-3">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Swift Ride. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">
                    <a href="#" class="text-light text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-light text-decoration-none">Terms of Service</a>
                </p>
            </div>
        </div>
    </div>
</footer>
