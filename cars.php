<?php
require_once 'includes/auth.php';
$conn = Connect();

// Current date for comparison
$today = date('Y-m-d');

// Optional filters (from GET parameters)
$brandFilter = isset($_GET['brand']) ? $_GET['brand'] : '';
$locationFilter = isset($_GET['location']) ? $_GET['location'] : '';
$fuelFilter = isset($_GET['fuel']) ? $_GET['fuel'] : '';
$priceSort = isset($_GET['price_sort']) ? $_GET['price_sort'] : ''; // "asc" or "desc"

// Base SQL query → only Cars
$sql = "
    SELECT v.*, l.city, l.branch
    FROM vehicles v
    LEFT JOIN bookings b ON v.vehicle_id = b.vehicle_id AND b.status='confirmed'
    LEFT JOIN locations l ON v.location_id = l.location_id
    WHERE v.vehicle_type = 'Car' 
      AND (b.booking_id IS NULL OR DATE_ADD(b.return_date, INTERVAL 1 DAY) < ?)
";

$params = [$today];
$types = "s";

// Add filters dynamically
if ($brandFilter !== '') {
    $sql .= " AND v.vehicle_brand = ?";
    $types .= "s";
    $params[] = $brandFilter;
}

if ($locationFilter !== '') {
    $sql .= " AND l.city = ?";
    $types .= "s";
    $params[] = $locationFilter;
}

if ($fuelFilter !== '') {
    $sql .= " AND v.fuel_type = ?";
    $types .= "s";
    $params[] = $fuelFilter;
}

// Price sorting
$orderBy = "v.vehicle_brand, v.vehicle_model"; // default
if ($priceSort === "asc") {
    $orderBy = "v.price ASC";
} elseif ($priceSort === "desc") {
    $orderBy = "v.price DESC";
}

$sql .= " ORDER BY $orderBy";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all unique brands, locations, fuels → but only for Cars
$brands = $conn->query("SELECT DISTINCT vehicle_brand FROM vehicles WHERE vehicle_type='Car' ORDER BY vehicle_brand")->fetch_all(MYSQLI_ASSOC);
$locations = $conn->query("SELECT DISTINCT city FROM locations ORDER BY city")->fetch_all(MYSQLI_ASSOC);
$fuelTypes = $conn->query("SELECT DISTINCT fuel_type FROM vehicles WHERE vehicle_type='Car' ORDER BY fuel_type")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cars - Car Rental System</title>
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
    <h2 class="mb-4">Available Cars</h2>

    <!-- Filters -->
    <form class="row g-3 mb-4" method="GET">
        <div class="col-md-2">
            <select name="brand" class="form-select">
                <option value="">All Brands</option>
                <?php foreach($brands as $b): ?>
                    <option value="<?= htmlspecialchars($b['vehicle_brand']) ?>" <?= $brandFilter==$b['vehicle_brand']?'selected':'' ?>>
                        <?= htmlspecialchars($b['vehicle_brand']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="location" class="form-select">
                <option value="">All Locations</option>
                <?php foreach($locations as $l): ?>
                    <option value="<?= htmlspecialchars($l['city']) ?>" <?= $locationFilter==$l['city']?'selected':'' ?>>
                        <?= htmlspecialchars($l['city']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="fuel" class="form-select">
                <option value="">All Fuel Types</option>
                <?php foreach($fuelTypes as $f): ?>
                    <option value="<?= htmlspecialchars($f['fuel_type']) ?>" <?= $fuelFilter==$f['fuel_type']?'selected':'' ?>>
                        <?= htmlspecialchars($f['fuel_type']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="price_sort" class="form-select">
                <option value="">Sort by Price</option>
                <option value="asc" <?= $priceSort=='asc'?'selected':'' ?>>Low to High</option>
                <option value="desc" <?= $priceSort=='desc'?'selected':'' ?>>High to Low</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <!-- Vehicle Cards -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($vehicle = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php
                        // ✅ Use the saved path directly
                        $img = !empty($vehicle['vehicle_image']) ? $vehicle['vehicle_image'] : 'assets/img/cars/default.jpg';
                        ?>
                        <img src="<?= htmlspecialchars($img) ?>" class="vehicle-img-top" alt="<?= htmlspecialchars($vehicle['vehicle_brand'].' '.$vehicle['vehicle_model']) ?>">

                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($vehicle['vehicle_brand'].' '.$vehicle['vehicle_model']) ?></h5>
                            <div class="mb-3">
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-gas-pump me-1"></i><?= htmlspecialchars($vehicle['fuel_type']) ?>
                                </span>
                                <span class="badge bg-<?= $vehicle['vehicle_availability']==='yes'?'success':'danger' ?>">
                                    <i class="fas fa-<?= $vehicle['vehicle_availability']==='yes'?'check':'times' ?> me-1"></i>
                                    <?= $vehicle['vehicle_availability']==='yes'?'Available':'Not Available' ?>
                                </span>
                            </div>
                            <div class="mb-3">
                                Rs. <?= number_format($vehicle['price'],2) ?> <small class="text-muted">/ day</small>
                            </div>
                            <?php if (is_user_logged_in()): ?>
                                <a href="my_bookings.php?vehicle_id=<?= $vehicle['vehicle_id'] ?>" class="btn btn-primary w-100">
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
                    <i class="fas fa-info-circle me-2"></i>No cars available at the moment.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
