<?php
// booking_details.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (!function_exists('is_user_logged_in')) {
    function is_user_logged_in() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!is_user_logged_in()) {
    header('Location: login.php');
    exit();
}

$conn = Connect();
if (!$conn) {
    die('Database connection failed. Please check your configuration.');
}

$user_id = intval($_SESSION['user_id']);
$username = $_SESSION['name'] ?? 'User';
$message = "";

// ✅ Automatically mark completed trips (past return date)
$conn->query("
    UPDATE bookings
    SET status='completed'
    WHERE user_id = $user_id
      AND status='confirmed'
      AND return_date < CURDATE()
");

// ✅ Fetch user's bookings with feedback info
$stmt = $conn->prepare("
    SELECT b.*, v.vehicle_brand, v.vehicle_model, v.vehicle_id, b.feedback_given
    FROM bookings b 
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id 
    WHERE b.user_id = ? 
    ORDER BY b.booking_date DESC
");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Details - Vehicle Rental System</title>
<?php include 'includes/header.php'; ?>
</head>
<body class="bg-light">
<?php include 'includes/navigation.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">Booking Details</h2>
    <p class="mb-4">Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong></p>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($bookings && count($bookings) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Booking ID</th>
                        <th>Vehicle</th>
                        <th>Pickup Date</th>
                        <th>Return Date</th>
                        <th>Pickup Location</th>
                        <th>Dropoff Location</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Booking Date</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['vehicle_brand'] . ' ' . $booking['vehicle_model']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($booking['pickup_date']))); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($booking['return_date']))); ?></td>
                            <td><?php echo htmlspecialchars($booking['pickup_location']); ?></td>
                            <td><?php echo htmlspecialchars($booking['dropoff_location']); ?></td>
                            <td><?php echo htmlspecialchars('$' . number_format($booking['total_amount'], 2)); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($booking['booking_date']))); ?></td>

                            <!-- Feedback -->
                            <td>
                                <?php if ($booking['status'] === 'completed' && $booking['feedback_given'] === 'no'): ?>
                                    <form method="POST" action="submit_review.php" class="d-flex flex-column">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                        <input type="hidden" name="vehicle_id" value="<?php echo $booking['vehicle_id']; ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                                        <label>Rating (1-5):</label>
                                        <input type="number" name="rating" min="1" max="5" required class="form-control mb-1">
                                        <label>Comment:</label>
                                        <input type="text" name="comment" required class="form-control mb-1">
                                        <button type="submit" name="submit_review" class="btn btn-sm btn-primary mt-1">Submit</button>
                                    </form>
                                <?php elseif ($booking['feedback_given'] === 'yes'): ?>
                                    ✅ Submitted
                                <?php else: ?>
                                    –
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No bookings found.</div>
    <?php endif; ?>

    <?php $conn->close(); ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
