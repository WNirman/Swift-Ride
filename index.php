<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/auth.php';
require_once 'includes/config.php';

try {
    $conn = Connect();

    $types_array = ['Car','Van','Bike','Auto Rickshaw','SUV','Pickup','Bus','Truck','Scooter','Bicycle'];

    $locations_result = $conn->query("SELECT city FROM Locations");
    $locations_array = [];
    while ($row = $locations_result->fetch_assoc()) {
        $locations_array[] = $row['city'];
    }

    $price_sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM Vehicles";
    $price_result = $conn->query($price_sql);
    $price_range = $price_result->fetch_assoc();

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 6;
    $offset = ($page - 1) * $per_page;

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

    $count_sql = "SELECT COUNT(*) as total FROM Vehicles WHERE " . implode(" AND ", $where_clauses);
    $stmt = $conn->prepare($count_sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_vehicles = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_vehicles / $per_page);

    $sql = "SELECT * FROM Vehicles 
            WHERE " . implode(" AND ", $where_clauses) . " 
            ORDER BY vehicle_brand LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $types .= "ii";
    $params[] = $per_page;
    $params[] = $offset;
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $available_vehicles = $stmt->get_result();

} catch (Exception $e) {
    error_log("Error in index.php: " . $e->getMessage());
    $error_message = "An error occurred while fetching vehicles. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Find Your Ride - Rent Any Vehicle</title>
<link rel="icon" type="image/png" href="assets/img/favicon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
body { font-family: 'Poppins', sans-serif; }

/* Hero Section */
.hero {
    position: relative;
    height: 70vh;
    display: flex;
    align-items: flex-start;       /* align content to top */
    justify-content: flex-start;   /* align content to left */
    overflow: hidden;
    padding: 1rem;                 /* less padding to move text closer to corner */
    background: #000;              /* fallback if bg image fails */
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 45%;
    margin-top: 0;                  /* remove extra top margin */
    margin-left: 0;                 /* remove extra left margin */
}

.hero-text {
    font-family: 'Playfair Display', serif;
    font-size: 3.5rem;
    font-weight: 700;
    color: #f0eaeaff;
    line-height: 1.2;
    margin-bottom: 0.5rem;
    transition: transform 0.1s ease, opacity 0.2s ease;
}

.hero-subtext {
    font-family: 'Poppins', sans-serif;
    font-size: 1.2rem;
    color: #f5ededff;
    margin: 0;                       /* remove extra margin */
    transition: transform 0.1s ease, opacity 0.2s ease;
}

.hero-bg {
    position: absolute;
    top: 0;
    left: 0;                         /* cover from left */
    width: 100%;
    height: 100%;
    background-image: url('assets/img/bg1.webp');
    background-size: cover;
    background-position: center;
    opacity: 0.8;
    z-index: 1;
}



/* Vehicle cards */
.vehicle-card { transition: transform 0.3s ease; box-shadow: 0 2px 15px rgba(0,0,0,0.1); }
.vehicle-card:hover { transform: translateY(-5px); }
.vehicle-features { display: flex; gap: 1rem; color: #666; }
.vehicle-feature { display: flex; align-items: center; gap: 0.5rem; }
.filter-section { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
</style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
<?php include 'includes/navigation.php'; ?>

<!-- Dynamic Hero Section -->
<div class="hero" id="hero">
    <div class="hero-content">
        <h1 class="hero-text" id="hero-text">Find Your Perfect Ride</h1>
        <p class="hero-subtext">Rent cars, bikes, vans & more. Anywhere. Anytime.</p>
    </div>
    <div class="hero-bg" id="hero-bg"></div>
</div>

<div class="container my-5" id="available-vehicles">
<h2 class="text-center mb-4">Available Vehicles</h2>

<div class="filter-section">
<form method="GET" action="" class="row g-3">
<div class="col-md-3">
<input type="text" name="search" class="form-control" placeholder="Search vehicles..." 
       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
</div>
<div class="col-md-3">
<select name="type" class="form-select">
    <option value="">All Vehicle Types</option>
    <?php foreach ($types_array as $type): ?>
        <option value="<?php echo htmlspecialchars($type); ?>" 
            <?php echo isset($_GET['type']) && $_GET['type']===$type?'selected':''; ?>>
            <?php echo htmlspecialchars($type); ?>
        </option>
    <?php endforeach; ?>
</select>
</div>
<div class="col-md-3">
<select name="location" class="form-select">
    <option value="">All Locations</option>
    <?php foreach ($locations_array as $loc): ?>
        <option value="<?php echo htmlspecialchars($loc); ?>" 
            <?php echo isset($_GET['location']) && $_GET['location']===$loc?'selected':''; ?>>
            <?php echo htmlspecialchars($loc); ?>
        </option>
    <?php endforeach; ?>
</select>
</div>
<div class="col-md-2">
<div class="row">
    <div class="col-6">
        <input type="number" name="min_price" class="form-control" placeholder="Min Price"
               value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>"
               min="<?php echo floor($price_range['min_price']); ?>"
               max="<?php echo ceil($price_range['max_price']); ?>">
    </div>
    <div class="col-6">
        <input type="number" name="max_price" class="form-control" placeholder="Max Price"
               value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>"
               min="<?php echo floor($price_range['min_price']); ?>"
               max="<?php echo ceil($price_range['max_price']); ?>">
    </div>
</div>
</div>
<div class="col-md-1">
<button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
</div>
</form>
</div>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
<?php else: ?>
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
<?php if ($available_vehicles && $available_vehicles->num_rows > 0): ?>
    <?php while($vehicle = $available_vehicles->fetch_assoc()): ?>
        <div class="col">
            <div class="card vehicle-card h-100">
            <img src="<?php 
                echo !empty($vehicle['vehicle_image']) 
                    ? htmlspecialchars($vehicle['vehicle_image']) 
                    : 'assets/img/cars/nexon.jpg'; 
            ?>" 
            class="card-img-top" style="height:200px; object-fit:cover;" 
            alt="<?php echo htmlspecialchars($vehicle['vehicle_brand'].' '.$vehicle['vehicle_model']); ?>">

                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($vehicle['vehicle_brand'].' '.$vehicle['vehicle_model']); ?></h5>
                    <div class="vehicle-features mb-3">
                        <span class="vehicle-feature"><i class="fas fa-car"></i> <?php echo htmlspecialchars($vehicle['vehicle_type']); ?></span>
                        <span class="vehicle-feature"><i class="fas fa-map-marker-alt"></i> 
                            <?php
                            $loc_result = $conn->query("SELECT city FROM Locations WHERE location_id = ".$vehicle['location_id']);
                            $loc = $loc_result->fetch_assoc();
                            echo htmlspecialchars($loc['city']);
                            ?>
                        </span>
                        <span class="vehicle-feature"><i class="fas fa-check-circle"></i> Available</span>
                    </div>
                    <div class="vehicle-price mb-3">
                        <h4 class="text-primary mb-0">Rs. <?php echo number_format($vehicle['price'],2); ?> <small class="text-muted">/day</small></h4>
                    </div>
                    <?php if (is_user_logged_in()): ?>
                        <a href="my_bookings.php?vehicle_id=<?php echo $vehicle['vehicle_id']; ?>" class="btn btn-primary w-100"><i class="fas fa-calendar-check me-2"></i> Book Now</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary w-100"><i class="fas fa-sign-in-alt me-2"></i> Login to Book</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="col-12"><div class="alert alert-info text-center"><i class="fas fa-car-side fa-3x mb-3"></i>No vehicles available matching your criteria.</div></div>
<?php endif; ?>
</div>
<?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Parallax & scroll effect for hero
const heroText = document.getElementById("hero-text");
const heroSub = document.querySelector(".hero-subtext");

document.getElementById("hero").addEventListener("mousemove", (e) => {
    const x = (e.clientX / window.innerWidth) - 0.5;
    const y = (e.clientY / window.innerHeight) - 0.5;
    heroText.style.transform = translate(${x * 20}px, ${y * 10}px);
    if(heroSub) heroSub.style.transform = translate(${x * 10}px, ${y * 5}px);
});

window.addEventListener("scroll", () => {
    const scrollY = window.scrollY;
    const opacity = Math.max(0.2, 1 - scrollY / 400);
    heroText.style.opacity = opacity;
    if(heroSub) heroSub.style.opacity = opacity;
});
</script>
</body>
</html>