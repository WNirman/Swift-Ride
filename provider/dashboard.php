<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';

// Ensure provider login
if (!is_provider_logged_in()) {
    header("Location: provider_login.php");
    exit;
}

$provider_id = $_SESSION['provider_id'];

// Get optional success message
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';

// Database connection
$conn = Connect();

// Fetch provider stats
// Total vehicles
$result = $conn->query("SELECT COUNT(*) AS count FROM vehicles WHERE provider_id = $provider_id");
$totalVehicles = $result->fetch_assoc()['count'];

// Active bookings
$result = $conn->query("
    SELECT COUNT(*) AS count 
    FROM bookings b
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    WHERE v.provider_id = $provider_id AND b.status = 'confirmed'
");
$activeBookings = $result->fetch_assoc()['count'];

// Pending requests
$result = $conn->query("
    SELECT COUNT(*) AS count 
    FROM bookings b
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    WHERE v.provider_id = $provider_id AND b.status = 'pending'
");
$pendingRequests = $result->fetch_assoc()['count'];

// Total earnings
$result = $conn->query("
    SELECT SUM(b.total_amount) AS total
    FROM bookings b
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    WHERE v.provider_id = $provider_id AND b.status='confirmed'
");
$totalEarnings = $result->fetch_assoc()['total'] ?? 0;

// Fetch provider feedbacks (sorted by rating descending)
$feedback_stmt = $conn->prepare("
    SELECT f.feedback_id, f.rating, f.comment, f.created_at,
           b.booking_id, u.name AS customer_name,
           v.vehicle_brand, v.vehicle_model
    FROM feedbacks f
    JOIN bookings b ON f.booking_id = b.booking_id
    JOIN users u ON f.user_id = u.user_id
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    WHERE v.provider_id = ?
    ORDER BY f.rating DESC
");
$feedback_stmt->bind_param("i", $provider_id);
$feedback_stmt->execute();
$feedback_result = $feedback_stmt->get_result();
$feedbacks = $feedback_result->fetch_all(MYSQLI_ASSOC);
$feedback_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Dashboard</title>
    <link href="../style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>
    <script>
        function confirmDelete(vehicleName) {
            return confirm("Are you sure you want to delete " + vehicleName + "?");
        }
    </script>
</head>
<body>
    <?php include __DIR__ . '/../includes/provider_navbar.php'; ?>

    <div class="container main-content mt-4">
        <?php if(!empty($msg)): ?>
        <div class="alert alert-success">
            <?= $msg ?>
        </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h5>Total Vehicles</h5>
                    <h3><?= $totalVehicles ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h5>Active Bookings</h5>
                    <h3><?= $activeBookings ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h5>Pending Requests</h5>
                    <h3><?= $pendingRequests ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h5>Total Earnings</h5>
                    <h3>$<?= number_format($totalEarnings ?? 0, 2) ?></h3>
                </div>
            </div>
        </div>

        <!-- Vehicle Management -->
        <div class="card mb-4 p-3">
            <div class="d-flex justify-content-between mb-2">
                <h5>Your Vehicles</h5>
                <a href="add_vehicle.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Vehicle</a>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Vehicle Model</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Rate/Day</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("SELECT * FROM vehicles WHERE provider_id = $provider_id");
                    while ($vehicle = $stmt->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $vehicle['vehicle_model'] ?></td>
                            <td><?= $vehicle['vehicle_type'] ?></td>
                            <td>
                                <span class="badge bg-<?= $vehicle['vehicle_availability']=='yes'?'success':'warning' ?>"><?= ucfirst($vehicle['vehicle_availability']) ?></span>
                            </td>
                            <td>$<?= number_format($vehicle['price'], 2) ?></td>
                            <td>
                                <a href="edit_vehicle.php?id=<?= $vehicle['vehicle_id'] ?>" class="btn btn-sm btn-light">Edit</a>
                                <a href="delete_vehicle.php?id=<?= $vehicle['vehicle_id'] ?>" class="btn btn-sm btn-danger" onclick='return confirmDelete("<?= $vehicle['vehicle_model'] ?>");'>Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Recent Bookings -->
        <div class="card mb-4 p-3">
            <h5>Recent Booking Requests</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Dates</th>
                        <th>Status</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("
                        SELECT b.*, u.name AS customer, v.vehicle_model AS vehicle
                        FROM bookings b
                        JOIN users u ON b.user_id = u.user_id
                        JOIN vehicles v ON b.vehicle_id = v.vehicle_id
                        WHERE v.provider_id = $provider_id
                        ORDER BY b.booking_date DESC LIMIT 5
                    ");
                    while ($booking = $stmt->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $booking['customer'] ?></td>
                            <td><?= $booking['vehicle'] ?></td>
                            <td><?= $booking['pickup_date'] ?> - <?= $booking['return_date'] ?></td>
                            <td><span class="badge bg-info"><?= ucfirst($booking['status']) ?></span></td>
                            <td>
                                <a href="approve_booking.php?id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-success">Approve</a>
                                <a href="reject_booking.php?id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-danger">Reject</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Feedback Section -->
        <div class="card mb-4 p-3">
            <h5>Customer Feedbacks (Sorted by Rating Descending)</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Booking ID</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($feedbacks as $fb): ?>
                        <tr>
                            <td><?= htmlspecialchars($fb['customer_name']) ?></td>
                            <td><?= htmlspecialchars($fb['vehicle_brand'] . ' ' . $fb['vehicle_model']) ?></td>
                            <td><?= $fb['booking_id'] ?></td>
                            <td><?= $fb['rating'] ?>/5</td>
                            <td><?= htmlspecialchars($fb['comment']) ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($fb['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(count($feedbacks) == 0): ?>
                        <tr><td colspan="6" class="text-center">No feedbacks yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
