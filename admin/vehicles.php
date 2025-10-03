<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// ✅ Check if admin is logged in
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}

// ✅ Database connection using mysqli
$conn = Connect();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all vehicles with provider name
$sql = "SELECT v.vehicle_id, v.vehicle_brand, v.vehicle_model, v.vehicle_year, v.seats, v.fuel_type, 
               v.transmission, v.price, p.name AS provider_name
        FROM vehicles v
        JOIN providers p ON v.provider_id = p.provider_id
        ORDER BY v.vehicle_type ASC, v.vehicle_brand ASC";
$result = $conn->query($sql);

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">All Vehicles</h1>
        <a href="dashboard.php" class="btn btn-sm btn-secondary">Back to Dashboard</a>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Vehicle ID</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Year</th>
                                <th>Seats</th>
                                <th>Fuel Type</th>
                                <th>Transmission</th>
                                <th>Price/Day</th>
                                <th>Provider</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vehicle = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($vehicle['vehicle_id']); ?></td>
                                    <td><?= htmlspecialchars($vehicle['vehicle_brand']); ?></td>
                                    <td><?= htmlspecialchars($vehicle['vehicle_model']); ?></td>
                                    <td><?= htmlspecialchars($vehicle['vehicle_year']); ?></td>
                                    <td><?= htmlspecialchars($vehicle['seats']); ?></td>
                                    <td><?= htmlspecialchars($vehicle['fuel_type']); ?></td>
                                    <td><?= htmlspecialchars($vehicle['transmission']); ?></td>
                                    <td>$<?= number_format($vehicle['price'], 2); ?></td>
                                    <td><?= htmlspecialchars($vehicle['provider_name']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>No vehicles found.
        </div>
    <?php endif; ?>
</div>

<?php 
$conn->close();
include 'includes/footer.php';
?>
