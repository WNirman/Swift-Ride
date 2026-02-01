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
    function is_user_logged_in()
    {
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

    <div class="main-content">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-5 animate-up">
                <div>
                    <h2 class="fw-bold m-0 text-primary">Your Bookings</h2>
                    <p class="text-muted mb-0">Manage and track your vehicle rentals</p>
                </div>
                <div class="glass px-4 py-2 rounded-pill">
                    <span class="small text-muted">Welcome,</span>
                    <span class="fw-bold text-dark"><?= htmlspecialchars($username) ?></span>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info border-0 shadow-sm rounded-4 animate-up"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($bookings && count($bookings) > 0): ?>
                <div class="glass p-4 rounded-4 shadow-sm animate-up" style="animation-delay: 0.1s;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-muted small text-uppercase">
                                <tr>
                                    <th class="border-0">ID</th>
                                    <th class="border-0">Vehicle</th>
                                    <th class="border-0">Duration</th>
                                    <th class="border-0">Locations</th>
                                    <th class="border-0">Amount</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking):
                                    $status_class = 'bg-secondary';
                                    if ($booking['status'] === 'confirmed')
                                        $status_class = 'bg-primary';
                                    elseif ($booking['status'] === 'completed')
                                        $status_class = 'bg-success';
                                    elseif ($booking['status'] === 'cancelled')
                                        $status_class = 'bg-danger';
                                    ?>
                                    <tr>
                                        <td class="fw-bold text-muted small">
                                            #<?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                        <td>
                                            <div class="fw-bold">
                                                <?php echo htmlspecialchars($booking['vehicle_brand'] . ' ' . $booking['vehicle_model']); ?>
                                            </div>
                                            <div class="text-muted small">ID:
                                                <?php echo htmlspecialchars($booking['vehicle_id']); ?></div>
                                        </td>
                                        <td>
                                            <div class="small fw-bold text-dark">
                                                <?php echo htmlspecialchars(date('M d', strtotime($booking['pickup_date']))); ?>
                                                -
                                                <?php echo htmlspecialchars(date('M d', strtotime($booking['return_date']))); ?>
                                            </div>
                                            <div class="text-muted" style="font-size: 11px;">
                                                <?php echo htmlspecialchars(date('Y', strtotime($booking['pickup_date']))); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small"><i class="fas fa-map-marker-alt text-primary me-1"
                                                    style="font-size: 10px;"></i>
                                                <?php echo htmlspecialchars($booking['pickup_location']); ?></div>
                                            <div class="small text-muted"><i class="fas fa-arrow-down me-1"
                                                    style="font-size: 10px;"></i>
                                                <?php echo htmlspecialchars($booking['dropoff_location']); ?></div>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold text-dark">$<?php echo number_format($booking['total_amount'], 2); ?></span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge <?php echo $status_class; ?> rounded-pill px-3 py-2 small fw-bold">
                                                <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($booking['status'] === 'completed' && $booking['feedback_given'] === 'no'): ?>
                                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#feedback-<?php echo $booking['booking_id']; ?>">
                                                    Give Feedback
                                                </button>
                                            <?php elseif ($booking['feedback_given'] === 'yes'): ?>
                                                <span class="text-success small fw-bold"><i
                                                        class="fas fa-check-circle me-1"></i>Submitted</span>
                                            <?php else: ?>
                                                <span class="text-muted small">–</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php if ($booking['status'] === 'completed' && $booking['feedback_given'] === 'no'): ?>
                                        <tr class="collapse" id="feedback-<?php echo $booking['booking_id']; ?>">
                                            <td colspan="7" class="border-0 p-0">
                                                <div class="p-4 bg-light rounded-4 my-3 mx-2">
                                                    <h6 class="fw-bold mb-3">Rate your experience</h6>
                                                    <form method="POST" action="submit_review.php">
                                                        <input type="hidden" name="booking_id"
                                                            value="<?php echo $booking['booking_id']; ?>">
                                                        <input type="hidden" name="vehicle_id"
                                                            value="<?php echo $booking['vehicle_id']; ?>">
                                                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                                                        <div class="row g-3">
                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold text-muted">Rating
                                                                    (1-5)</label>
                                                                <input type="number" name="rating" min="1" max="5" required
                                                                    class="form-control border-0 shadow-sm">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label small fw-bold text-muted">Your
                                                                    Comment</label>
                                                                <input type="text" name="comment" required
                                                                    class="form-control border-0 shadow-sm"
                                                                    placeholder="Tell us about the vehicle...">
                                                            </div>
                                                            <div class="col-md-3 d-flex align-items-end">
                                                                <button type="submit" name="submit_review"
                                                                    class="btn btn-primary w-100 rounded-pill shadow-sm">Submit
                                                                    Review</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="glass p-5 rounded-4 text-center animate-up">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No bookings found</h4>
                    <p class="text-muted mb-4">Ready to start your journey?</p>
                    <a href="cars.php" class="btn btn-primary rounded-pill px-5 py-3 shadow">Explore Vehicles</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php $conn->close(); ?>

    <?php include 'includes/footer.php'; ?>
</body>

</html>