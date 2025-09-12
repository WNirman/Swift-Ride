<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';

if (!is_provider_logged_in()) {
    header("Location: provider_login.php");
    exit;
}

$provider_id = $_SESSION['provider_id'];
$conn = Connect();

// Fetch all bookings for provider's vehicles
$bookings = $conn->query("
    SELECT b.*, u.name AS customer, v.vehicle_model AS vehicle
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    WHERE v.provider_id = $provider_id
    ORDER BY b.booking_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Bookings</title>
    <link href="../style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../includes/provider_navbar.php'; ?>

    <div class="container main-content">
        <h3 class="mb-4">All Booking Requests</h3>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($b = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['customer']) ?></td>
                        <td><?= htmlspecialchars($b['vehicle']) ?></td>
                        <td><?= $b['pickup_date'] ?> - <?= $b['return_date'] ?></td>
                        <td>
                            <?php
                                if($b['status']=='pending') echo "<span class='badge bg-warning'>Pending</span>";
                                elseif($b['status']=='confirmed') echo "<span class='badge bg-success'>Confirmed</span>";
                                elseif($b['status']=='cancelled') echo "<span class='badge bg-danger'>Cancelled</span>";
                            ?>
                        </td>
                        <td>
                            <?php if($b['status']=='pending'): ?>
                                <a href='approve_booking.php?id=<?= $b['booking_id'] ?>' class='btn btn-sm btn-success'>Approve</a>
                                <a href='reject_booking.php?id=<?= $b['booking_id'] ?>' class='btn btn-sm btn-danger'>Reject</a>
                            <?php else: ?>
                                <span class='text-muted'>No Action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
