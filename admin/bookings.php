<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// ✅ Check if user is logged in and is admin
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}

// ✅ Get database connection (PDO)
$conn = ConnectPDO(); // 👉 Make sure in config.php you have a ConnectPDO() function returning a PDO object

// Set current page for nav highlighting
$current_page = 'bookings';
$page_title = 'Manage Bookings';

// ✅ Handle booking status updates and deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    if (isset($_POST['status'])) {
        // Update booking status
        $status = $_POST['status'];

        $sql = "UPDATE bookings SET status = :status WHERE booking_id = :booking_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Booking status updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update booking status.";
        }
    } elseif (isset($_POST['delete_booking'])) {
        // Delete booking
        $sql = "DELETE FROM bookings WHERE booking_id = :booking_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Booking deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete booking.";
        }
    }

    header('Location: bookings.php');
    exit;
}

// ✅ Query bookings with joins
$sql = "
    SELECT b.*, 
           u.name AS user_name,
           u.email AS user_email,
           v.vehicle_type,
           CONCAT(v.vehicle_brand, ' ', v.vehicle_model) AS vehicle_name,
           v.price
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.user_id
    LEFT JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    ORDER BY b.booking_date DESC
";
$stmt = $conn->query($sql);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="main-content">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Manage Bookings</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print Report
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header py-3 bg-white">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 fw-bold text-primary">All Bookings</h6>
                    </div>
                    <div class="col-auto">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchBookings" placeholder="Search bookings...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Booking ID</th>
                                <th>User</th>
                                <th>Vehicle</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Total Cost</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($bookings): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td class="ps-4">#<?= $booking['booking_id']; ?></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold"><?= htmlspecialchars($booking['user_name']); ?></span>
                                                <small class="text-muted"><?= htmlspecialchars($booking['user_email'] ?? ''); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold"><?= htmlspecialchars($booking['vehicle_name'] ?? ''); ?></span>
                                                <small class="text-muted"><?= htmlspecialchars($booking['vehicle_type'] ?? ''); ?></small>
                                            </div>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($booking['pickup_date'])); ?></td>
                                        <td><?= date('M d, Y', strtotime($booking['return_date'])); ?></td>
                                        <td>Rs. 
                                            <?php 
                                            $total = calculate_rental_cost(
                                                $booking['price'] ?? 0, 
                                                $booking['pickup_date'], 
                                                $booking['return_date']
                                            );
                                            echo number_format($total, 2); 
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                <?php 
                                                echo match($booking['status']) {
                                                    'pending' => 'bg-warning',
                                                    'confirmed' => 'bg-success',
                                                    'cancelled' => 'bg-danger',
                                                    'completed' => 'bg-info',
                                                    default => 'bg-secondary'
                                                };
                                                ?>">
                                                <?= ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-edit me-1"></i>Status
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <?php foreach (['pending'=>'clock','confirmed'=>'check','cancelled'=>'ban','completed'=>'flag-checkered'] as $status => $icon): ?>
                                                            <li>
                                                                <form method="POST" class="dropdown-item d-flex align-items-center">
                                                                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                                                                    <input type="hidden" name="status" value="<?= $status; ?>">
                                                                    <button type="submit" class="btn btn-link p-0 text-decoration-none text-<?= $status === 'pending' ? 'warning' : ($status === 'confirmed' ? 'success' : ($status === 'cancelled' ? 'danger' : 'info')) ?>">
                                                                        <i class="fas fa-<?= $icon; ?> me-2"></i><?= ucfirst($status); ?>
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteBookingModal<?= $booking['booking_id']; ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Booking Modal -->
                                            <div class="modal fade" id="deleteBookingModal<?= $booking['booking_id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Delete Booking</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <p>Are you sure you want to delete this booking?</p>
                                                            <p class="mb-0"><strong>Booking ID:</strong> #<?= $booking['booking_id']; ?></p>
                                                            <p class="mb-0"><strong>User:</strong> <?= htmlspecialchars($booking['user_name']); ?></p>
                                                            <p class="mb-0"><strong>Vehicle:</strong> <?= htmlspecialchars($booking['vehicle_name'] ?? ''); ?></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                                                                <input type="hidden" name="delete_booking" value="1">
                                                                <button type="submit" class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">No bookings found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchBookings');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
    }
});
</script>

<?php 
$conn = null; // ✅ Close PDO connection
include 'includes/footer.php'; 
?>
