<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}

$conn = Connect();

// Get all vehicle types
$sql = "SELECT vehicle_type, COUNT(*) as total_models 
        FROM Vehicles 
        GROUP BY vehicle_type 
        ORDER BY vehicle_type ASC";
$result = $conn->query($sql);

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Vehicle Types</h1>
    </div>

    <div class="row">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['vehicle_type']); ?></h5>
                            <p class="text-muted mb-2">Models: <?php echo $row['total_models']; ?></p>
                            <a href="view_vehicles.php?type=<?php echo urlencode($row['vehicle_type']); ?>" 
                               class="btn btn-sm btn-primary">
                                View Vehicles
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No vehicle types found.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$conn->close();
include 'includes/footer.php';
?>