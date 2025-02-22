<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/auth.php';
require_once 'includes/config.php';

try {
    $conn = Connect();

    // Get car types for filter
    $types_sql = "SELECT DISTINCT car_type FROM cars ORDER BY car_type";
    $car_types = $conn->query($types_sql);
    $types_array = [];
    if ($car_types) {
        while ($type = $car_types->fetch_assoc()) {
            $types_array[] = $type['car_type'];
        }
    }

    // Get price range
    $price_sql = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM cars";
    $price_result = $conn->query($price_sql);
    $price_range = $price_result->fetch_assoc();

    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 6;
    $offset = ($page - 1) * $per_page;

    // Build query based on filters
    $where_clauses = ["car_availability = 'yes'"];
    $params = [];
    $types = "";

    if (!empty($_GET['search'])) {
        $where_clauses[] = "(car_name LIKE ? OR car_type LIKE ?)";
        $search_term = "%" . $_GET['search'] . "%";
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= "ss";
    }

    if (!empty($_GET['type'])) {
        $where_clauses[] = "car_type = ?";
        $params[] = $_GET['type'];
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

    // Count total cars for pagination
    $count_sql = "SELECT COUNT(*) as total FROM cars WHERE " . implode(" AND ", $where_clauses);
    $stmt = $conn->prepare($count_sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_cars = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_cars / $per_page);

    // Get filtered cars
    $sql = "SELECT * FROM cars 
            WHERE " . implode(" AND ", $where_clauses) . " 
            ORDER BY car_name LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $types .= "ii";
    $params[] = $per_page;
    $params[] = $offset;
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $available_cars = $stmt->get_result();

} catch (Exception $e) {
    error_log("Error in index.php: " . $e->getMessage());
    $error_message = "An error occurred while fetching cars. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Car Rental System</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .hero-section {
            background: url('assets/img/nepal.jpg') no-repeat center center;
            background-size: cover;
            padding: 120px 0 80px;
            position: relative;
            min-height: 600px;
            display: flex;
            align-items: center;
        }
        .hero-overlay {
            background: rgba(0,0,0,0.6);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }
        .car-card {
            transition: transform 0.3s ease;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .car-card:hover {
            transform: translateY(-5px);
        }
        .car-features {
            display: flex;
            gap: 1rem;
            color: #666;
        }
        .car-feature {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
    <?php include 'includes/navigation.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section text-white text-center position-relative">
        <div class="hero-overlay"></div>
        <div class="container py-5">
            <div class="row py-5 align-items-center">
                <div class="col-lg-8 mx-auto position-relative" style="z-index: 2;">
                    <h1 class="display-4 fw-bold mb-4">Find Your Perfect Ride</h1>
                    <p class="lead mb-4">Choose from our wide selection of cars for any occasion. Easy booking, great rates!</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-center">
                        <a href="#available-cars" class="btn btn-primary btn-lg px-4 gap-3">
                            <i class="fas fa-car me-2"></i>Browse Cars
                        </a>
                        <?php if (!is_user_logged_in()): ?>
                            <a href="register.php" class="btn btn-outline-light btn-lg px-4">
                                <i class="fas fa-user-plus me-2"></i>Sign Up
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Available Cars Section -->
    <div id="available-cars" class="container my-5">
        <h2 class="text-center mb-4">Available Cars</h2>
        
        <!-- Search and Filter -->
        <div class="filter-section">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search cars..." 
                               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <?php foreach ($types_array as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" 
                                    <?php echo isset($_GET['type']) && $_GET['type'] === $type ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
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
                    <a href="index.php" class="btn btn-secondary w-100" title="Reset Filters">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center">
                <?php echo $error_message; ?>
            </div>
        <?php else: ?>
            <!-- Cars Grid -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php if ($available_cars && $available_cars->num_rows > 0): ?>
                    <?php while($car = $available_cars->fetch_assoc()): ?>
                        <div class="col">
                            <div class="card car-card h-100">
                                <img src="<?php echo !empty($car['car_image']) ? $car['car_image'] : 'assets/img/cars/default.jpg'; ?>" 
                                     class="card-img-top" style="height: 200px; object-fit: cover;"
                                     alt="<?php echo htmlspecialchars($car['car_name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($car['car_name']); ?></h5>
                                    <div class="car-features mb-3">
                                        <span class="car-feature">
                                            <i class="fas fa-car"></i> <?php echo htmlspecialchars($car['car_type']); ?>
                                        </span>
                                        <span class="car-feature">
                                            <i class="fas fa-check-circle"></i> Available
                                        </span>
                                    </div>
                                    <div class="car-price mb-3">
                                        <h4 class="text-primary mb-0">
                                            Rs. <?php echo number_format($car['price'], 2); ?>
                                            <small class="text-muted">/day</small>
                                        </h4>
                                    </div>
                                    <?php if (is_user_logged_in()): ?>
                                        <a href="book.php?car_id=<?php echo $car['car_id']; ?>" class="btn btn-primary w-100">
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
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-car-side fa-3x mb-3"></i>
                            <p class="mb-0">No cars available matching your criteria.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php 
                                    echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; 
                                    echo isset($_GET['type']) ? '&type=' . urlencode($_GET['type']) : '';
                                    echo isset($_GET['min_price']) ? '&min_price=' . urlencode($_GET['min_price']) : '';
                                    echo isset($_GET['max_price']) ? '&max_price=' . urlencode($_GET['max_price']) : '';
                                ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Price range validation
        const minPrice = document.querySelector('input[name="min_price"]');
        const maxPrice = document.querySelector('input[name="max_price"]');

        if (minPrice && maxPrice) {
            minPrice.addEventListener('change', function() {
                if (maxPrice.value && Number(this.value) > Number(maxPrice.value)) {
                    this.value = maxPrice.value;
                }
            });

            maxPrice.addEventListener('change', function() {
                if (minPrice.value && Number(this.value) < Number(minPrice.value)) {
                    this.value = minPrice.value;
                }
            });
        }

        // Smooth scroll to available cars section
        document.querySelector('a[href="#available-cars"]').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('#available-cars').scrollIntoView({ behavior: 'smooth' });
        });
    </script>
</body>
</html>