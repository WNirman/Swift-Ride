<?php
// booking.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (!is_user_logged_in()) {
    header('Location: login.php');
    exit();
}

$conn = Connect();
if (!$conn) {
    die('Database connection failed.');
}

$user_id = intval($_SESSION['user_id']);
$username = $_SESSION['username'] ?? 'User';
$message = "";
$vehicle = null;

// Vehicle ID comes from GET (clicked "Book Now" button)
if (!empty($_GET['vehicle_id'])) {
    $vehicle_id = intval($_GET['vehicle_id']);
    $stmt = $conn->prepare("SELECT vehicle_id, vehicle_brand, vehicle_model, price 
                            FROM vehicles 
                            WHERE vehicle_id=? AND vehicle_availability='yes'");
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $stmt->close();
} else {
    // No vehicle_id? Go back to homepage
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];
    $pickup_location = $_POST['pickup_location'];
    $dropoff_location = $_POST['dropoff_location'];

    if ($vehicle) {
        $price_per_day = $vehicle['price'];
        $days = (strtotime($return_date) - strtotime($pickup_date)) / 86400;
        if ($days < 1) $days = 1;
        $total_amount = $days * $price_per_day;

        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings 
            (user_id, vehicle_id, pickup_date, return_date, pickup_location, dropoff_location, total_amount, status, booking_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("iissssd", $user_id, $vehicle['vehicle_id'], $pickup_date, $return_date, $pickup_location, $dropoff_location, $total_amount);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success text-center'>
                            Booking request submitted successfully!<br>
                            Your request has been sent to the provider.
                        </div>
                        <script>
                            setTimeout(function(){
                                window.location.href = 'index.php'; 
                            }, 3000);
                        </script>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Vehicle - Vehicle Rental System</title>
    <?php include 'includes/header.php'; ?>
</head>
<body class="bg-light">
<?php include 'includes/navigation.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">Book Vehicle</h2>
    <p class="mb-4">Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong></p>

    <?php echo $message; ?>

    <?php if ($vehicle): ?>
    <form method="POST" class="card p-4 shadow-sm">
        <!-- Vehicle details -->
        <div class="mb-3">
            <label class="form-label">Vehicle</label>
            <input type="text" class="form-control" 
                   value="<?php echo htmlspecialchars($vehicle['vehicle_brand'].' '.$vehicle['vehicle_model']); ?>" readonly>
            <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['vehicle_id']; ?>">
        </div>

        <!-- Pickup & Return Dates -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="pickup_date" class="form-label">Pickup Date</label>
                <input type="date" name="pickup_date" id="pickup_date" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="return_date" class="form-label">Return Date</label>
                <input type="date" name="return_date" id="return_date" class="form-control" required>
            </div>
        </div>

        <!-- Pickup & Dropoff Locations -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="pickup_location" class="form-label">Pickup Location</label>
                <input type="text" name="pickup_location" id="pickup_location" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="dropoff_location" class="form-label">Dropoff Location</label>
                <input type="text" name="dropoff_location" id="dropoff_location" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-car me-1"></i> Confirm Booking
        </button>
    </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
