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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the vehicle type from the URL parameter
$vehicle_type = isset($_GET['type']) ? $_GET['type'] : '';

// Fetch vehicle details including provider name based on the vehicle type
$sql = "SELECT v.vehicle_id, v.vehicle_brand, v.vehicle_model, v.vehicle_year, v.seats, v.fuel_type, v.transmission, v.price, p.name AS provider_name 
        FROM Vehicles v 
        JOIN providers p ON v.provider_id = p.provider_id 
        WHERE v.vehicle_type = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $vehicle_type);
$stmt->execute();
$result = $stmt->get_result();

include 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?php echo htmlspecialchars(ucfirst($vehicle_type)); ?> Details</h1>
        <a href="vehicle_types.php" class="btn btn-sm btn-secondary">Back to Vehicle Types</a>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Vehicle ID</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Year</th>
                            <th>Seats</th>
                            <th>Fuel Type</th>
                            <th>Transmission</th>
                            <th>Price</th>
                            <th>Provider Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($vehicle = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vehicle['vehicle_id']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['vehicle_brand']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['vehicle_model']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['vehicle_year']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['seats']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['fuel_type']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['transmission']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['price']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['provider_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No vehicles found for <?php echo htmlspecialchars(ucfirst($vehicle_type)); ?>.
        </div>
    <?php endif; ?>
</div>

<?php 
$stmt->close();
$conn->close();
include 'includes/footer.php';
?>