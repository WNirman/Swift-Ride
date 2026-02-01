<?php
require_once 'includes/auth.php';
$conn = Connect();

// Current date for comparison
$today = date('Y-m-d');

// Vehicle type for this page
$typeFilter = 'Van';

// Optional filters (from GET parameters)
$vanType = isset($_GET['van_type']) ? $_GET['van_type'] : '';
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

// Van type filter (using vehicle_model as category)
if ($vanType !== '' && $vanType !== 'all') {
    $sql .= " AND v.vehicle_model = ?";
    $types .= "s";
    $params[] = $vanType;
}

// Location filter
if ($locationFilter !== '' && $locationFilter !== 'all') {
    $sql .= " AND l.city = ?";
    $types .= "s";
    $params[] = $locationFilter;
}

// Price sorting
$orderBy = "v.vehicle_model"; // default
if ($priceSort === "asc")
    $orderBy = "v.price ASC";
elseif ($priceSort === "desc")
    $orderBy = "v.price DESC";

$sql .= " ORDER BY $orderBy";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Fetch unique van types and locations
$vanTypes = $conn->query("SELECT DISTINCT vehicle_model FROM vehicles WHERE vehicle_type='Van' ORDER BY vehicle_model")->fetch_all(MYSQLI_ASSOC);
$locations = $conn->query("SELECT DISTINCT city FROM locations ORDER BY city")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Go Big - Vans</title>
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

    <div class="main-content">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-5 animate-up">
                <h2 class="fw-bold m-0 text-primary">Go Big - <span><?= htmlspecialchars($typeFilter) ?>s</span></h2>
            </div>

            <!-- Filters Section -->
            <div class="glass p-4 rounded-4 mb-5 animate-up" style="animation-delay: 0.1s;">
                <form class="row g-3" method="GET">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Van Type</label>
                        <select name="van_type" class="form-select border-0 bg-light">
                            <option value="all">All Models</option>
                            <?php foreach ($vanTypes as $v): ?>
                                <option value="<?= htmlspecialchars($v['vehicle_model']) ?>"
                                    <?= $vanType == $v['vehicle_model'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($v['vehicle_model']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Location</label>
                        <select name="location" class="form-select border-0 bg-light">
                            <option value="all">All Locations</option>
                            <?php foreach ($locations as $l): ?>
                                <option value="<?= htmlspecialchars($l['city']) ?>"
                                    <?= $locationFilter == $l['city'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($l['city']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Price Range</label>
                        <select name="price_sort" class="form-select border-0 bg-light">
                            <option value="">Sort by Price</option>
                            <option value="asc" <?= $priceSort == 'asc' ? 'selected' : '' ?>>Low to High</option>
                            <option value="desc" <?= $priceSort == 'desc' ? 'selected' : '' ?>>High to Low</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            <i class="fas fa-search me-2"></i>Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Vehicle Cards -->
            <div class="row row-cols-1 row-cols-md-3 g-4 animate-up" style="animation-delay: 0.2s;">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($vehicle = $result->fetch_assoc()): ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden vehicle-card-modern">
                                <?php $img = !empty($vehicle['vehicle_image'])
                                    ? $vehicle['vehicle_image']
                                    : 'assets/img/default-van.jpg'; ?>
                                <div class="position-relative">
                                    <img src="<?= htmlspecialchars($img) ?>" class="card-img-top"
                                        style="height: 220px; object-fit: cover;"
                                        alt="<?= htmlspecialchars($vehicle['vehicle_brand'] . ' ' . $vehicle['vehicle_model']) ?>">
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge glass text-dark py-2 px-3 rounded-pill fw-bold">
                                            Rs. <?= number_format($vehicle['price']) ?> / day
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body p-4">
                                    <h5 class="fw-bold mb-2">
                                        <?= htmlspecialchars($vehicle['vehicle_brand'] . ' ' . $vehicle['vehicle_model']) ?></h5>
                                    <div class="d-flex align-items-center mb-4 text-muted small">
                                        <span class="me-3"><i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                            <?= htmlspecialchars($vehicle['city'] ?? 'Anywhere') ?></span>
                                        <span><i class="fas fa-shuttle-van me-1 text-primary"></i> Van</span>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mt-auto">
                                        <?php if ($vehicle['vehicle_availability'] === 'yes'): ?>
                                            <span class="text-success small fw-bold"><i class="fas fa-circle me-1"
                                                    style="font-size: 8px;"></i> Available</span>
                                        <?php else: ?>
                                            <span class="text-secondary small fw-bold"><i class="fas fa-circle me-1"
                                                    style="font-size: 8px;"></i> Booked</span>
                                        <?php endif; ?>

                                        <?php if (is_user_logged_in()): ?>
                                            <a href="my_bookings.php?vehicle_id=<?= $vehicle['vehicle_id'] ?>"
                                                class="btn btn-primary btn-sm px-4 rounded-pill">
                                                Book Now
                                            </a>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-outline-primary btn-sm px-4 rounded-pill">
                                                Login to Book
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="glass p-5 rounded-4">
                            <i class="fas fa-shuttle-van fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No vans found</h4>
                            <p class="text-muted mb-0">Try adjusting your filters to find your perfect ride.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    </div>

    <?php include 'includes/footer.php'; ?>

</body>

</html>