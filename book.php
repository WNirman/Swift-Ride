<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
}

// Get car details
if (isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];
    $conn = Connect();
    $sql = "SELECT * FROM cars WHERE car_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    
    if (!$car) {
        header('Location: cars.php');
        exit();
    }
} else {
    header('Location: cars.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?php echo htmlspecialchars($car['car_name']); ?> - Car Rental System</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .booking-form {
            max-width: 800px;
            margin: 0 auto;
        }
        .car-image {
            max-height: 300px;
            object-fit: contain;
            width: 100%;
        }
        .rental-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .rental-summary hr {
            margin: 15px 0;
        }
        .total-amount {
            font-size: 1.25rem;
            color: #0d6efd;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'includes/navigation.php'; ?>

    <div class="container py-5">
        <div class="booking-form">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h3 class="card-title h5 mb-0">Book <?php echo htmlspecialchars($car['car_name']); ?></h3>
                </div>
                <div class="card-body">
                    <!-- Car Details -->
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6 text-center mb-3 mb-md-0">
                            <img src="<?php echo !empty($car['car_image']) ? $car['car_image'] : 'assets/img/cars/default.jpg'; ?>" 
                                 class="car-image rounded" 
                                 alt="<?php echo htmlspecialchars($car['car_name']); ?>">
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-3"><?php echo htmlspecialchars($car['car_name']); ?></h4>
                            <div class="mb-3">
                                <span class="badge bg-secondary me-2"><?php echo htmlspecialchars($car['car_type']); ?></span>
                                <span class="badge bg-success">Available</span>
                            </div>
                            <div class="rental-summary">
                                <h5 class="mb-3">Rental Summary</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Daily Rate:</span>
                                    <span>Rs. <?php echo number_format($car['price'], 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Number of Days:</span>
                                    <span id="totalDays">0</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Total Amount:</span>
                                    <span id="totalAmount" class="total-amount fw-bold">Rs. 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <form action="process_booking.php" method="POST" id="bookingForm">
                        <input type="hidden" name="car_id" value="<?php echo htmlspecialchars($car_id); ?>">
                        <input type="hidden" name="total_days" id="hidden_total_days" value="0">
                        <input type="hidden" name="total_amount" id="hidden_total_amount" value="0">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="pickup_date" class="form-label">Pickup Date</label>
                                <input type="date" class="form-control" id="pickup_date" name="pickup_date" required 
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="return_date" class="form-label">Return Date</label>
                                <input type="date" class="form-control" id="return_date" name="return_date" required
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                            
                            <div class="col-12">
                                <label for="pickup_location" class="form-label">Pickup Location</label>
                                <input type="text" class="form-control" id="pickup_location" name="pickup_location" 
                                       placeholder="Enter your preferred pickup location" required>
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Special Requests (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Any special requirements or notes for your booking"></textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-calendar-check me-2"></i>Confirm Booking
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pickupDate = document.getElementById('pickup_date');
            const returnDate = document.getElementById('return_date');
            const totalDays = document.getElementById('totalDays');
            const totalAmount = document.getElementById('totalAmount');
            const hiddenTotalDays = document.getElementById('hidden_total_days');
            const hiddenTotalAmount = document.getElementById('hidden_total_amount');
            const pricePerDay = <?php echo $car['price']; ?>;

            function updatePrice() {
                if (pickupDate.value && returnDate.value) {
                    const start = new Date(pickupDate.value);
                    const end = new Date(returnDate.value);
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    
                    if (diffDays >= 0) {
                        totalDays.textContent = diffDays;
                        const total = diffDays * pricePerDay;
                        totalAmount.textContent = 'Rs. ' + total.toLocaleString('en-IN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        
                        // Update hidden fields
                        hiddenTotalDays.value = diffDays;
                        hiddenTotalAmount.value = total;
                    }
                }
            }

            // Set default dates
            const today = new Date().toISOString().split('T')[0];
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];
            
            pickupDate.value = today;
            returnDate.value = tomorrowStr;
            
            updatePrice(); // Initial calculation

            pickupDate.addEventListener('change', function() {
                const nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);
                returnDate.min = nextDay.toISOString().split('T')[0];
                
                if (returnDate.value && new Date(returnDate.value) <= new Date(this.value)) {
                    returnDate.value = nextDay.toISOString().split('T')[0];
                }
                
                updatePrice();
            });

            returnDate.addEventListener('change', function() {
                const minDate = new Date(pickupDate.value);
                minDate.setDate(minDate.getDate() + 1);
                
                if (new Date(this.value) < minDate) {
                    this.value = minDate.toISOString().split('T')[0];
                }
                
                updatePrice();
            });

            // Form validation
            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                if (!pickupDate.value || !returnDate.value) {
                    e.preventDefault();
                    alert('Please select both pickup and return dates');
                    return;
                }

                const start = new Date(pickupDate.value);
                const end = new Date(returnDate.value);
                if (end <= start) {
                    e.preventDefault();
                    alert('Return date must be after pickup date');
                    return;
                }
            });
        });
    </script>
</body>
</html>
