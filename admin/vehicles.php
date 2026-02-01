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
        WHERE v.is_deleted = 0
        ORDER BY v.vehicle_type ASC, v.vehicle_brand ASC";
$result = $conn->query($sql);

include 'includes/header.php';
?>

<div class="main-content">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5 animate-up">
            <div>
                <h2 class="fw-bold m-0 text-primary">All Vehicles</h2>
                <p class="text-muted mb-0">Manage and oversee your entire fleet</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill px-4 transition-hover">
                <i class="fas fa-arrow-left me-2"></i> Dashboard
            </a>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="glass rounded-4 shadow-sm border-0 animate-up overflow-hidden" style="animation-delay: 0.1s;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th class="border-0 px-4">Vehicle ID</th>
                                <th class="border-0">Vehicle Details</th>
                                <th class="border-0">Specs</th>
                                <th class="border-0">Provider</th>
                                <th class="border-0 text-end px-4">Price/Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vehicle = $result->fetch_assoc()): ?>
                                <tr class="transition-hover">
                                    <td class="px-4"><span
                                            class="fw-bold text-muted small">#<?= htmlspecialchars($vehicle['vehicle_id']); ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <?= htmlspecialchars($vehicle['vehicle_brand'] . ' ' . $vehicle['vehicle_model']); ?>
                                        </div>
                                        <div class="text-muted small">Year: <?= htmlspecialchars($vehicle['vehicle_year']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span
                                                class="badge glass text-dark small"><?= htmlspecialchars($vehicle['seats']); ?>
                                                Seats</span>
                                            <span
                                                class="badge glass text-dark small"><?= htmlspecialchars($vehicle['fuel_type']); ?></span>
                                            <span
                                                class="badge glass text-dark small"><?= htmlspecialchars($vehicle['transmission']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-secondary small">
                                            <?= htmlspecialchars($vehicle['provider_name']); ?>
                                        </div>
                                    </td>
                                    <td class="text-end px-4">
                                        <span class="fw-bold text-primary">$<?= number_format($vehicle['price'], 2); ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="glass p-5 rounded-4 text-center animate-up">
                <i class="fas fa-car-side fa-3x text-muted mb-3 opacity-50"></i>
                <h5 class="text-muted">No vehicles found in the system.</h5>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>