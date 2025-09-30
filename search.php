<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/auth.php';
require_once 'includes/config.php';

try {
    $conn = Connect();

    $where_clauses = ["vehicle_availability = 'yes'"];
    $params = [];
    $types = "";

    if (!empty($_GET['search'])) {
        $where_clauses[] = "(vehicle_brand LIKE ? OR vehicle_model LIKE ? OR vehicle_type LIKE ?)";
        $search_term = "%" . $_GET['search'] . "%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= "sss";
    }

    if (!empty($_GET['type'])) {
        $where_clauses[] = "vehicle_type = ?";
        $params[] = $_GET['type'];
        $types .= "s";
    }

    if (!empty($_GET['location'])) {
        $where_clauses[] = "location_id = (SELECT location_id FROM Locations WHERE city = ? LIMIT 1)";
        $params[] = $_GET['location'];
        $types .= "s";
    }

    if (!empty($_GET['min_price'])) {
        $where_clauses[] = "price >= ?";
        $params[] = $_GET['min_price'];
        $types .= "d";
    }

    if (!empty($_GET['max_price'])) {
        $where_clauses[] = "price <= ?";
        $params[] = $_GET['max_price'];
        $types .= "d";
    }

    $sql = "SELECT * FROM Vehicles WHERE " . implode(" AND ", $where_clauses) . " ORDER BY vehicle_brand";
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

} catch (Exception $e) {
    die("Error in search.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Search Results - Swift Ride</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Search Results</h2>
    <a href="index.php" class="btn btn-secondary mb-3">⬅ Back</a>
    <div class="row g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($vehicle = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?php echo !empty($vehicle['vehicle_image']) ? htmlspecialchars($vehicle['vehicle_image']) : 'assets/img/cars/nexon.jpg'; ?>" 
                             class="card-img-top" style="height:200px; object-fit:cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($vehicle['vehicle_brand'].' '.$vehicle['vehicle_model']); ?></h5>
                            <p class="card-text">Type: <?php echo htmlspecialchars($vehicle['vehicle_type']); ?><br>
                            Price: Rs. <?php echo number_format($vehicle['price'], 2); ?> /day</p>
                            <a href="index.php" class="btn btn-primary">View More</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">No vehicles found matching your search.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
