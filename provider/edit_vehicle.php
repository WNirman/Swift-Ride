<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';

$conn = Connect(); // Make sure $conn is defined

// Check if provider is logged in
if(!isset($_SESSION['provider_id'])){
    header("Location: provider_login.php");
    exit();
}

// Get vehicle ID from URL
if(!isset($_GET['id'])){
    header("Location: add_vehicle.php"); // redirect to vehicle list page
    exit();
}

$vehicle_id = intval($_GET['id']); // always sanitize input

// Fetch vehicle details
$query = "SELECT * FROM Vehicles WHERE vehicle_id=$vehicle_id AND provider_id=".$_SESSION['provider_id'];
$result = mysqli_query($conn, $query);

if(!$result || mysqli_num_rows($result) == 0){
    echo "Vehicle not found or you don't have permission to edit.";
    exit();
}

$vehicle = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Vehicle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Vehicle</h2>
    <form action="update_vehicle.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="vehicle_id" value="<?= $vehicle['vehicle_id']; ?>">

        <div class="mb-3">
            <label for="vehicle_model" class="form-label">Model</label>
            <input type="text" id="vehicle_model" name="vehicle_model" class="form-control" value="<?= $vehicle['vehicle_model']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="vehicle_type" class="form-label">Type</label>
            <input type="text" id="vehicle_type" name="vehicle_type" class="form-control" value="<?= $vehicle['vehicle_type']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price per Day</label>
            <input type="number" id="price" name="price" class="form-control" value="<?= $vehicle['price']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="availability" class="form-label">Availability</label>
            <select name="vehicle_availability" class="form-select">
                <option value="yes" <?= $vehicle['vehicle_availability']=='yes'?'selected':'' ?>>Available</option>
                <option value="no" <?= $vehicle['vehicle_availability']=='no'?'selected':'' ?>>Not Available</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="vehicle_image" class="form-label">Vehicle Image</label>
            <input type="file" name="vehicle_image" class="form-control">
            <?php if(!empty($vehicle['vehicle_image'])): ?>
                <img src="../<?= $vehicle['vehicle_image']; ?>" alt="Vehicle Image" width="150" class="mt-2">
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Update Vehicle</button>
        <a href="add_vehicle.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
