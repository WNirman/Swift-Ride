<?php
require_once 'includes/auth.php';
$conn = Connect();

// Fetch all vehicles
$sql = "SELECT * FROM Vehicles ORDER BY vehicle_brand, vehicle_model";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Vehicles - Car Rental System</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .vehicle-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
            object-position: center;
        }
    </style>
</head>
<body class="bg-light">
<?php include 'includes/navigation.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">Our Fleet</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($vehicle = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php
                        $img = !empty($vehicle['vehicle_image']) ? 'uploads/vehicles/' . $vehicle['vehicle_image'] : 'assets/img/cars/default.jpg';
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" class="vehicle-img-top" alt="<?php echo htmlspecialchars($vehicle['vehicle_brand'].' '.$vehicle['vehicle_model']); ?>">

                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($vehicle['vehicle_brand'].' '.$vehicle['vehicle_model']); ?></h5>
                            <div class="mb-3">
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-car me-1"></i><?php echo htmlspecialchars($vehicle['vehicle_type']); ?>
                                </span>
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-gas-pump me-1"></i><?php echo htmlspecialchars($vehicle['fuel_type']); ?>
                                </span>
                                <span class="badge bg-<?php echo $vehicle['vehicle_availability']==='yes'?'success':'danger'; ?>">
                                    <i class="fas fa-<?php echo $vehicle['vehicle_availability']==='yes'?'check':'times'; ?> me-1"></i>
                                    <?php echo $vehicle['vehicle_availability']==='yes'?'Available':'Not Available'; ?>
                                </span>
                            </div>
                            <div class="mb-3">
                                Rs. <?php echo number_format($vehicle['price'],2); ?> <small class="text-muted">/ day</small>
                            </div>
                            <?php if (is_user_logged_in()): ?>
                                <a href="book.php?vehicle_id=<?php echo $vehicle['vehicle_id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-calendar-check me-2"></i>Book Now
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login to Book
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No vehicles available at the moment.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
