
<?php
require_once 'includes/auth.php';
$conn = Connect();

// Current date for comparison
$today = date('Y-m-d');

// Vehicle type for this page
$typeFilter = 'Bus';

// Optional filters (from GET parameters)
$busType = isset($_GET['bus_type']) ? $_GET['bus_type'] : '';
$locationFilter = isset($_GET['location']) ? $_GET['location'] : '';
$priceSort = isset($_GET['price_sort']) ? $_GET['price_sort'] : ''; // "asc" or "desc"

// Base SQL query
$sql = "
    SELECT v.*, l.city, l.branch
    FROM vehicles v
    LEFT JOIN bookings b ON v.vehicle_id = b.vehicle_id AND b.status='confirmed'
    LEFT JOIN locations l ON v.location_id = l.location_id
    WHERE v.vehicle_type = ? AND (b.booking_id IS NULL OR DATE_ADD(b.return_date, INTERVAL 1 DAY) < ?)
";

$params = [$typeFilter, $today];
$types = "ss";

// Bus type filter (using vehicle_model as category)
if ($busType !== '' && $busType !== 'all') {
    $sql .= " AND v.vehicle_model = ?";
    $types .= "s";
    $params[] = $busType;
}

// Location filter
if ($locationFilter !== '' && $locationFilter !== 'all') {
    $sql .= " AND l.city = ?";
    $types .= "s";
    $params[] = $locationFilter;
}

// Price sorting
$orderBy = "v.vehicle_model"; // default
if ($priceSort === "asc") $orderBy = "v.price ASC";
elseif ($priceSort === "desc") $orderBy = "v.price DESC";

$sql .= " ORDER BY $orderBy";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Fetch unique bus types and locations
$busTypes = $conn->query("SELECT DISTINCT vehicle_model FROM vehicles WHERE vehicle_type='Bus' ORDER BY vehicle_model")->fetch_all(MYSQLI_ASSOC);
$locations = $conn->query("SELECT DISTINCT city FROM locations ORDER BY city")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Go Big - Buses</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .vehicle-img-top { width: 100%; height: 200px; object-fit: cover; object-position: center; }
    </style>
</head>
<body class="bg-light">
<?php include 'includes/navigation.php'; ?>

<div class="container py-5">
    <h2 class="mb-4 text-danger">Go Big - Buses</h2>

    <!-- Filters Section -->
    <form class="row g-3 mb-4" method="GET">
        <div class="col-md-3">
            <select name="bus_type" class="form-select">
                <option value="all">All Bus Types</option>
                <?php foreach($busTypes as $b): ?>
                    <option value="<?= htmlspecialchars($b['vehicle_model']) ?>" <?= $busType==$b['vehicle_model']?'selected':'' ?>>
                        <?= htmlspecialchars($b['vehicle_model']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="location" class="form-select">
                <option value="all">All Locations</option>
                <?php foreach($locations as $l): ?>
                    <option value="<?= htmlspecialchars($l['city']) ?>" <?= $locationFilter==$l['city']?'selected':'' ?>>
                        <?= htmlspecialchars($l['city']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="price_sort" class="form-select">
                <option value="">Sort by Price</option>
                <option value="asc" <?= $priceSort=='asc'?'selected':'' ?>>Low to High</option>
                <option value="desc" <?= $priceSort=='desc'?'selected':'' ?>>High to Low</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-danger w-100">Find Your Bus</button>
        </div>
    </form>

    <!-- Vehicle Cards -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($vehicle = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php $img = !empty($vehicle['vehicle_image']) 
                            ? $vehicle['vehicle_image'] 
                            : 'assets/img/buses/default.jpg'; ?>
                        <img src="<?= htmlspecialchars($img) ?>" class="vehicle-img-top" 
                            alt="<?= htmlspecialchars($vehicle['vehicle_brand'].' '.$vehicle['vehicle_model']) ?>">

                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($vehicle['vehicle_brand'].' '.$vehicle['vehicle_model']) ?></h5>
                            <div class="mb-3">
                                <span class="badge bg-<?= $vehicle['vehicle_availability']==='yes'?'success':'danger' ?>">
                                    <i class="fas fa-<?= $vehicle['vehicle_availability']==='yes'?'check':'times' ?> me-1"></i>
                                    <?= $vehicle['vehicle_availability']==='yes'?'Available':'Not Available' ?>
                                </span>
                            </div>
                            <p class="mb-2"><strong>Seats:</strong> <?= htmlspecialchars($vehicle['seats']) ?></p>
                            <p class="mb-2"><strong>Location:</strong> <?= htmlspecialchars($vehicle['city'].' - '.$vehicle['branch']) ?></p>
                            <div class="mb-3">
                                Rs. <?= number_format($vehicle['price'],2) ?> <small class="text-muted">/ day</small>
                            </div>
                            <?php if (is_user_logged_in()): ?>
                                <a href="my_bookings.php?vehicle_id=<?= $vehicle['vehicle_id'] ?>" class="btn btn-danger w-100">
                                    <i class="fas fa-calendar-check me-1"></i>Book Now
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login to Book
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No buses available at the moment.
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
```
