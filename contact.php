<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

$conn = Connect();

// Create enquiries table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($create_table_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = sanitize_input($_POST['name']);
        $email = sanitize_input($_POST['email']);
        $subject = sanitize_input($_POST['subject']);
        $message = sanitize_input($_POST['message']);

        // Insert into enquiries table
        $sql = "INSERT INTO enquiries (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Your message has been sent successfully. We'll get back to you soon!";
            $_SESSION['message_type'] = 'success';
        } else {
            throw new Exception("Failed to insert message");
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Contact form error: " . $e->getMessage());
        $_SESSION['message'] = "Failed to send message. Please try again.";
        $_SESSION['message_type'] = 'danger';
    }

    $conn->close();
    header('Location: contact.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Car Rental System</title>
    <?php include 'includes/header.php'; ?>
</head>

<body class="bg-light">
    <?php include 'includes/navigation.php'; ?>

    <div class="main-content">
        <div class="container py-5 mt-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="row g-5">
                        <!-- Contact Info -->
                        <div class="col-md-4 animate-up">
                            <h2 class="fw-bold mb-4">Get in Touch</h2>
                            <p class="text-muted mb-5">Have questions? We're here to help you get the best ride
                                experience.</p>

                            <div class="glass p-4 rounded-4 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box-primary me-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Email us</div>
                                        <div class="fw-bold">support@swiftride.com</div>
                                    </div>
                                </div>
                            </div>

                            <div class="glass p-4 rounded-4 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box-primary me-3">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Call us</div>
                                        <div class="fw-bold">+1 (234) 567-890</div>
                                    </div>
                                </div>
                            </div>

                            <div class="glass p-4 rounded-4">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box-primary me-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Visit us</div>
                                        <div class="fw-bold">123 Street, Colombo</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Form -->
                        <div class="col-md-8 animate-up" style="animation-delay: 0.1s;">
                            <div class="glass p-5 rounded-4 shadow-sm h-100">
                                <h3 class="fw-bold mb-4">Send Message</h3>

                                <?php if (isset($_SESSION['message'])): ?>
                                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> border-0 shadow-sm rounded-4 alert-dismissible fade show mb-4"
                                        role="alert">
                                        <?php
                                        echo $_SESSION['message'];
                                        unset($_SESSION['message']);
                                        unset($_SESSION['message_type']);
                                        ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="">
                                    <div class="row g-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label small fw-bold text-muted">Your
                                                Name</label>
                                            <input type="text" class="form-control border-0 bg-light p-3 rounded-3"
                                                id="name" name="name" required placeholder="John Doe">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label small fw-bold text-muted">Email
                                                Address</label>
                                            <input type="email" class="form-control border-0 bg-light p-3 rounded-3"
                                                id="email" name="email" required placeholder="john@example.com">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="subject" class="form-label small fw-bold text-muted">Subject</label>
                                        <input type="text" class="form-control border-0 bg-light p-3 rounded-3"
                                            id="subject" name="subject" required placeholder="How can we help?">
                                    </div>
                                    <div class="mb-4">
                                        <label for="message" class="form-label small fw-bold text-muted">Message</label>
                                        <textarea class="form-control border-0 bg-light p-3 rounded-3" id="message"
                                            name="message" rows="5" required
                                            placeholder="Your message here..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 shadow-sm">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
</body>

</html>