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
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5 animate-up">
            <div>
                <h2 class="fw-bold m-0 text-primary">Manage Bookings</h2>
                <p class="text-muted mb-0">Track and manage all vehicle reservations</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 transition-hover">
                <i class="fas fa-arrow-left me-2"></i> Dashboard
            </a>
        </div>

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

        <!-- Search and Filters bar -->
        <div class="glass p-3 rounded-4 mb-4 animate-up shadow-sm border-0" style="animation-delay: 0.1s;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 opacity-50"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-0 bg-transparent" id="searchBookings" placeholder="Search by user, vehicle, or ID...">
                    </div>
                </div>
            </div>
        </div>

        <div class="glass rounded-4 shadow-sm border-0 animate-up overflow-hidden" style="animation-delay: 0.2s;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th class="border-0 px-4">ID</th>
                            <th class="border-0">User Info</th>
                            <th class="border-0">Vehicle Details</th>
                            <th class="border-0">Duration</th>
                            <th class="border-0 text-end px-4">Total Amount</th>
                            <th class="border-0 text-center px-4">Status</th>
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
                                <tr class="transition-hover">
                                    <td class="px-4"><span class="fw-bold text-muted small">#<?= $b['booking_id']; ?></span></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($b['user_name'] ?? '-'); ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($b['user_phone'] ?? '-'); ?></div>
                                        <div class="mt-1"><span class="badge glass text-primary" style="font-size: 10px;"><?= $booking_count ?> bookings</span></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($b['vehicle_brand'] . ' ' . $b['vehicle_model']); ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($b['vehicle_type']); ?></div>
                                        <div class="text-muted" style="font-size: 10px;">Provider: <?= htmlspecialchars($b['provider_name']); ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark"><?= format_date($b['pickup_date']); ?></div>
                                        <div class="text-muted small">to <?= format_date($b['return_date']); ?></div>
                                    </td>
                                    <td class="text-end px-4">
                                        <span class="fw-bold text-dark">Rs. <?= number_format($total_cost, 2); ?></span>
                                    </td>
                                    <td class="text-center px-4">
                                        <span class="badge rounded-pill px-3 py-2 small fw-bold
                                            <?= $b['status']=='pending'?'bg-warning text-dark':($b['status']=='confirmed'?'bg-primary':($b['status']=='cancelled'?'bg-danger':'bg-success')) ?>">
                                            <?= ucfirst($b['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">No bookings found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
