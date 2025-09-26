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
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = intval($_POST['vehicle_id']);
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];
    $pickup_location = $_POST['pickup_location'];
    $dropoff_location = $_POST['dropoff_location'];

    // Get vehicle price
    $stmt = $conn->prepare("SELECT price FROM vehicles WHERE vehicle_id=?");
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $stmt->close();

    if ($vehicle) {
        $price_per_day = $vehicle['price'];
        $days = (strtotime($return_date) - strtotime($pickup_date)) / 86400;
        if ($days < 1) $days = 1;
        $total_amount = $days * $price_per_day;

        // Insert booking
        $stmt = $conn->prepare("INSERT INTO bookings 
            (user_id, vehicle_id, pickup_date, return_date, pickup_location, dropoff_location, total_amount, status, booking_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("iissssd", $user_id, $vehicle_id, $pickup_date, $return_date, $pickup_location, $dropoff_location, $total_amount);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Booking request submitted successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}

// Load all vehicles to show in the select menu
$vehicles = [];
$res = $conn->query("SELECT vehicle_id, vehicle_brand, vehicle_model FROM vehicles");
if ($res && $res->num_rows > 0) {
    $vehicles = $res->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Vehicle - Vehicle Rental System</title>
    <?php include 'includes/header.php'; ?>
</head>
<body class="bg-light">
<?php include 'includes/navigation.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">Book a Vehicle</h2>

    <?php echo $message; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="vehicle_id" class="form-label">Select Vehicle</label>
            <select name="vehicle_id" id="vehicle_id" class="form-select" required>
                <option value="">-- Choose Vehicle --</option>
                <?php foreach ($vehicles as $v): ?>
                    <option value="<?php echo $v['vehicle_id']; ?>">
                        <?php echo htmlspecialchars($v['vehicle_brand'].' '.$v['vehicle_model']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="pickup_date" class="form-label">Pickup Date</label>
            <input type="date" name="pickup_date" id="pickup_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="return_date" class="form-label">Return Date</label>
            <input type="date" name="return_date" id="return_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="pickup_location" class="form-label">Pickup Location</label>
            <input type="text" name="pickup_location" id="pickup_location" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="dropoff_location" class="form-label">Dropoff Location</label>
            <input type="text" name="dropoff_location" id="dropoff_location" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-car me-1"></i> Confirm Booking
        </button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
