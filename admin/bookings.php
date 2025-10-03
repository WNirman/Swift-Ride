<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}

// Database connection
$conn = Connect();
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch bookings with user, vehicle, and provider info
$sql = "SELECT b.*, 
               u.name AS user_name,
               u.phone AS user_phone,
               u.created_at AS joined_date,
               v.vehicle_type,
               v.vehicle_brand,
               v.vehicle_model,
               v.price AS vehicle_price,
               p.name AS provider_name
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN vehicles v ON b.vehicle_id = v.vehicle_id
        JOIN providers p ON v.provider_id = p.provider_id
        ORDER BY b.booking_date DESC";
$result = $conn->query($sql);
$bookings = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

include 'includes/header.php';
?>

<div class="main-content">
    <div class="container-fluid p-4">
        <h1 class="h3 mb-4">Manage Bookings</h1>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Search bar -->
        <div class="mb-3">
            <input type="text" class="form-control" id="searchBookings" placeholder="Search bookings...">
        </div>

        <div class="card shadow">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Booking ID</th>
                                <th>User</th>
                                <th>Phone</th>
                                <th>User Bookings</th>
                                <th>Joined</th>
                                <th>Vehicle</th>
                                <th>Provider</th>
                                <th>Pickup Date</th>
                                <th>Return Date</th>
                                <th>Total Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($bookings): ?>
                                <?php foreach ($bookings as $b): ?>
                                    <?php
                                        $user_id = $b['user_id'];
                                        $booking_count = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE user_id = $user_id")->fetch_assoc()['count'];
                                        $total_cost = calculate_rental_cost($b['vehicle_price'], $b['pickup_date'], $b['return_date']);
                                    ?>
                                    <tr>
                                        <td><?= $b['booking_id']; ?></td>
                                        <td><?= htmlspecialchars($b['user_name']); ?></td>
                                        <td><?= htmlspecialchars($b['user_phone']); ?></td>
                                        <td><span class="badge bg-info"><?= $booking_count ?> bookings</span></td>
                                        <td><?= format_date($b['joined_date']); ?></td>
                                        <td><?= htmlspecialchars($b['vehicle_brand'] . ' ' . $b['vehicle_model'] . ' (' . $b['vehicle_type'] . ')'); ?></td>
                                        <td><?= htmlspecialchars($b['provider_name']); ?></td>
                                        <td><?= $b['pickup_date']; ?></td>
                                        <td><?= $b['return_date']; ?></td>
                                        <td><?= number_format($total_cost, 2); ?></td>
                                        <td>
                                            <span class="badge 
                                                <?= $b['status']=='pending'?'bg-warning':($b['status']=='confirmed'?'bg-success':($b['status']=='cancelled'?'bg-danger':'bg-info')) ?>">
                                                <?= ucfirst($b['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="11" class="text-center py-4">No bookings found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const searchInput = document.getElementById('searchBookings');
    searchInput.addEventListener('keyup', function(){
        const value = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
        });
    });
});
</script>

<?php
$conn->close();
include 'includes/footer.php';
?>
