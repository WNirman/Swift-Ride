<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}

// Get database connection
$conn = Connect();

// Set current page for nav highlighting
$current_page = 'dashboard';
$page_title = 'Vehicle Rental Admin'; // UPDATED: Page title changed from Car Rental Admin

// Initialize counts
$bookings_count = 0;
$users_count = 0;
$enquiries_count = 0;
$vehicle_types_count = 0; // Count for vehicles
$providers_count = 0;     // Count for providers

// Get counts using try-catch to handle potential errors
try {
    // Get all tables in the database
    $tables_result = $conn->query("SHOW TABLES");
    $tables = array();
    while ($table = $tables_result->fetch_row()) {
        $tables[] = $table[0];
    }

    // Bookings count
    if (in_array('bookings', $tables)) {
        $bookings_result = $conn->query("SELECT COUNT(*) as total FROM bookings");
        if ($bookings_result) {
            $bookings_count = $bookings_result->fetch_assoc()['total'];
        }
    }

    // Users count
    if (in_array('users', $tables)) {
        $users_result = $conn->query("SELECT COUNT(*) as total FROM users");
        if ($users_result) {
            $users_count = $users_result->fetch_assoc()['total'];
        }
    }

    // Recent enquiries count (last 30 days)
    if (in_array('enquiries', $tables)) {
        $columns_result = $conn->query("SHOW COLUMNS FROM enquiries LIKE 'created_at'");
        if ($columns_result->num_rows > 0) {
            $enquiries_result = $conn->query("SELECT COUNT(*) as total FROM enquiries WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
        } else {
            $enquiries_result = $conn->query("SELECT COUNT(*) as total FROM enquiries");
        }
        if ($enquiries_result) {
            $enquiries_count = $enquiries_result->fetch_assoc()['total'];
        }
    }

    $vehicle_types_count = 0;
    $veh_result = $conn->query("SELECT COUNT(DISTINCT vehicle_type) as total FROM Vehicles");
    if ($veh_result) {
        $vehicle_types_count = $veh_result->fetch_assoc()['total'];
    }

    

    // Providers count
    if (in_array('providers', $tables)) {
        $pr_result = $conn->query("SELECT COUNT(*) as total FROM providers");
        if ($pr_result) {
            $providers_count = $pr_result->fetch_assoc()['total'];
        }
    }

} catch (Exception $e) {
    error_log("Error in dashboard counts: " . $e->getMessage());
}

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Dashboard Overview</h1>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">

        <!-- Total Vehicles -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="stat-icon">
                            <i class="fas fa-list fa-2x text-secondary"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="h6 mb-2">Total Vehicles</h3>
                        <div class="count"><?php echo $vehicle_types_count; ?></div>
                        <p class="text-muted small mb-0">Defined Types</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="vehicle_types.php" class="btn btn-sm btn-light">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Total Providers -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="stat-icon">
                            <i class="fas fa-warehouse fa-2x text-dark"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="h6 mb-2">Providers</h3>
                        <div class="count"><?php echo $providers_count; ?></div>
                        <p class="text-muted small mb-0">Registered</p>
                    </div>
                </div>
                <div class="mt-3">
                   <a href="../providers.php" class="btn btn-sm btn-light">View Details <i class="fas fa-arrow-right ms-1"></i></a>

                </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="stat-icon">
                            <i class="fas fa-calendar-check fa-2x text-success"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="h6 mb-2">Total Bookings</h3>
                        <div class="count"><?php echo $bookings_count; ?></div>
                        <p class="text-muted small mb-0">All Time Bookings</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="bookings.php" class="btn btn-sm btn-light">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="stat-icon">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="h6 mb-2">Total Users</h3>
                        <div class="count"><?php echo $users_count; ?></div>
                        <p class="text-muted small mb-0">Registered Users</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="users.php" class="btn btn-sm btn-light">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Recent Enquiries -->
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <span class="stat-icon">
                            <i class="fas fa-envelope fa-2x text-warning"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="h6 mb-2">Recent Enquiries</h3>
                        <div class="count"><?php echo $enquiries_count; ?></div>
                        <p class="text-muted small mb-0">Last 30 Days</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="enquiries.php" class="btn btn-sm btn-light">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data Tables -->
    <div class="row g-4">
        <!-- Recently Added Bookings -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recently Added Bookings</h5>
                        <a href="bookings.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php
                    try {
                        if (in_array('bookings', $tables)) {
                            $bk_cols = $conn->query("SHOW COLUMNS FROM bookings");
                            $bk_columns = array();
                            while ($col = $bk_cols->fetch_assoc()) {
                                $bk_columns[] = $col['Field'];
                            }

                            $order_by = in_array('created_at', $bk_columns) ? 'created_at' : 'id';

                            $recent_bookings = $conn->query("
                                SELECT b.*, u.username 
                                FROM bookings b 
                                LEFT JOIN users u ON b.user_id = u.user_id 
                                ORDER BY $order_by DESC 
                                LIMIT 5
                            ");

                            if ($recent_bookings && $recent_bookings->num_rows > 0) {
                                echo '<div class="table-responsive">';
                                echo '<table class="table table-hover mb-0">';
                                echo '<thead class="table-light">';
                                echo '<tr>';
                                echo '<th class="border-0">Booking ID</th>';
                                echo '<th class="border-0">User</th>';
                                echo '<th class="border-0">Vehicle</th>';
                                echo '<th class="border-0">Date</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';
                                while ($booking = $recent_bookings->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($booking['booking_id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($booking['username'] ?? '-') . '</td>';
                                    echo '<td>' . htmlspecialchars($booking['vehicle_name'] ?? '-') . '</td>';
                                    echo '<td>' . htmlspecialchars($booking['created_at'] ?? '-') . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody>';
                                echo '</table>';
                                echo '</div>';
                            } else {
                                echo '<div class="text-center py-4">';
                                echo '<p class="text-muted mb-0">No bookings added yet</p>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="text-center py-4">';
                            echo '<p class="text-muted mb-0">Bookings table not found</p>';
                            echo '</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="text-center py-4">';
                        echo '<p class="text-danger mb-0">Error loading bookings</p>';
                        echo '</div>';
                        error_log("Error loading bookings: " . $e->getMessage());
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Recently Added Providers -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recently Added Providers</h5>
                        <a href="providers.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php
                    try {
                        if (in_array('providers', $tables)) {
                            $recent_providers = $conn->query("SELECT * FROM providers ORDER BY created_at DESC LIMIT 5");
                            if ($recent_providers && $recent_providers->num_rows > 0) {
                                echo '<div class="table-responsive">';
                                echo '<table class="table table-hover mb-0">';
                                echo '<thead class="table-light">';
                                echo '<tr>';
                                echo '<th class="border-0">Name</th>';
                                echo '<th class="border-0">Email</th>';
                                echo '<th class="border-0">Phone</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';
                                while ($row = $recent_providers->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['email'] ?? '-') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['phone'] ?? '-') . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody>';
                                echo '</table>';
                                echo '</div>';
                            } else {
                                echo '<div class="text-center py-4">';
                                echo '<p class="text-muted mb-0">No providers added yet</p>';
                                echo '</div>';
                            }
                        }
                    } catch (Exception $e) {
                        echo '<div class="text-center py-4">';
                        echo '<p class="text-danger mb-0">Error loading providers</p>';
                        echo '</div>';
                        error_log("Error loading providers: " . $e->getMessage());
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>