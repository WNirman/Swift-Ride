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

<div class="main-content">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5 animate-up">
            <div>
                <h2 class="fw-bold m-0 text-primary">Admin Dashboard</h2>
                <p class="text-muted mb-0">Overview of your rental system performance</p>
            </div>
            <div class="glass px-4 py-2 rounded-pill">
                <span class="small text-muted">System Active</span>
                <span class="ms-2"><i class="fas fa-circle text-success" style="font-size: 8px;"></i></span>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5 animate-up" style="animation-delay: 0.1s;">
            <!-- Total Vehicles -->
            <div class="col-sm-6 col-md-4 col-xl-2-5">
                <div class="glass p-4 rounded-4 shadow-sm h-100 transition-hover border-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-primary sm me-3">
                            <i class="fas fa-car"></i>
                        </div>
                        <span class="text-muted small fw-bold">Vehicles</span>
                    </div>
                    <div class="h2 fw-bold mb-1"><?php echo $vehicle_types_count; ?></div>
                    <div class="text-muted small">Managed Types</div>
                    <a href="vehicle_types.php" class="stretched-link"></a>
                </div>
            </div>

            <!-- Total Providers -->
            <div class="col-sm-6 col-md-4 col-xl-2-5">
                <div class="glass p-4 rounded-4 shadow-sm h-100 transition-hover border-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-primary sm me-3"
                            style="background: rgba(52, 58, 64, 0.1); color: #343a40;">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <span class="text-muted small fw-bold">Providers</span>
                    </div>
                    <div class="h2 fw-bold mb-1"><?php echo $providers_count; ?></div>
                    <div class="text-muted small">Registered</div>
                    <a href="../providers.php" class="stretched-link"></a>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="col-sm-6 col-md-4 col-xl-2-5">
                <div class="glass p-4 rounded-4 shadow-sm h-100 transition-hover border-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-primary sm me-3"
                            style="background: rgba(40, 167, 69, 0.1); color: #28a745;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <span class="text-muted small fw-bold">Bookings</span>
                    </div>
                    <div class="h2 fw-bold mb-1"><?php echo $bookings_count; ?></div>
                    <div class="text-muted small">Successful Rentals</div>
                    <a href="bookings.php" class="stretched-link"></a>
                </div>
            </div>

            <!-- Total Users -->
            <div class="col-sm-6 col-md-4 col-xl-2-5">
                <div class="glass p-4 rounded-4 shadow-sm h-100 transition-hover border-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-primary sm me-3"
                            style="background: rgba(23, 162, 184, 0.1); color: #17a2b8;">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="text-muted small fw-bold">Users</span>
                    </div>
                    <div class="h2 fw-bold mb-1"><?php echo $users_count; ?></div>
                    <div class="text-muted small">Total Members</div>
                    <a href="users.php" class="stretched-link"></a>
                </div>
            </div>

            <!-- Recent Enquiries -->
            <div class="col-sm-6 col-md-4 col-xl-2-5">
                <div class="glass p-4 rounded-4 shadow-sm h-100 transition-hover border-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box-primary sm me-3"
                            style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <span class="text-muted small fw-bold">Enquiries</span>
                    </div>
                    <div class="h2 fw-bold mb-1"><?php echo $enquiries_count; ?></div>
                    <div class="text-muted small">Last 30 Days</div>
                    <a href="enquiries.php" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <!-- Recent Data Tables -->
        <div class="row g-4 mb-5 animate-up" style="animation-delay: 0.2s;">
            <!-- Recently Added Bookings -->
            <div class="col-lg-6">
                <div class="glass p-4 rounded-4 shadow-sm h-100 border-0">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0">Recent Bookings</h5>
                        <a href="bookings.php" class="btn btn-primary btn-sm rounded-pill px-3">View All</a>
                    </div>
                    <div class="table-responsive">
                        <?php
                        try {
                            if (in_array('bookings', $tables)) {
                                // ... existing logic to fetch columns and bookings ...
                                $bk_cols = $conn->query("SHOW COLUMNS FROM bookings");
                                $bk_columns = array();
                                while ($col = $bk_cols->fetch_assoc()) {
                                    $bk_columns[] = $col['Field'];
                                }
                                $order_by = in_array('created_at', $bk_columns) ? 'created_at' : 'id';
                                $recent_bookings = $conn->query("SELECT b.*, u.username FROM bookings b LEFT JOIN users u ON b.user_id = u.user_id ORDER BY $order_by DESC LIMIT 5");

                                if ($recent_bookings && $recent_bookings->num_rows > 0) {
                                    echo '<table class="table table-hover align-middle mb-0">';
                                    echo '<thead class="text-muted small text-uppercase"><tr><th class="border-0">ID</th><th class="border-0">User</th><th class="border-0">Vehicle</th><th class="border-0">Date</th></tr></thead>';
                                    echo '<tbody>';
                                    while ($booking = $recent_bookings->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td class="fw-bold text-muted small">#' . htmlspecialchars($booking['booking_id']) . '</td>';
                                        echo '<td>' . htmlspecialchars($booking['username'] ?? '-') . '</td>';
                                        echo '<td>' . htmlspecialchars($booking['vehicle_name'] ?? '-') . '</td>';
                                        echo '<td><span class="text-muted" style="font-size: 12px;">' . htmlspecialchars($booking['created_at'] ?? '-') . '</span></td>';
                                        echo '</td>';
                                    }
                                    echo '</tbody></table>';
                                } else {
                                    echo '<div class="text-center py-4"><p class="text-muted mb-0">No bookings yet</p></div>';
                                }
                            }
                        } catch (Exception $e) {
                            echo '<p class="text-danger">Error loading data</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Recently Added Providers -->
            <div class="col-lg-6">
                <div class="glass p-4 rounded-4 shadow-sm h-100 border-0">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0">Recent Providers</h5>
                        <a href="providers.php" class="btn btn-primary btn-sm rounded-pill px-3">View All</a>
                    </div>
                    <div class="table-responsive">
                        <?php
                        try {
                            if (in_array('providers', $tables)) {
                                $recent_providers = $conn->query("SELECT * FROM providers ORDER BY created_at DESC LIMIT 5");
                                if ($recent_providers && $recent_providers->num_rows > 0) {
                                    echo '<table class="table table-hover align-middle mb-0">';
                                    echo '<thead class="text-muted small text-uppercase"><tr><th class="border-0">Name</th><th class="border-0">Email</th><th class="border-0">Joined</th></tr></thead>';
                                    echo '<tbody>';
                                    while ($row = $recent_providers->fetch_assoc()) {
                                        echo '<tr>';
                                        echo '<td class="fw-bold">' . htmlspecialchars($row['name']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['email'] ?? '-') . '</td>';
                                        echo '<td><span class="text-muted" style="font-size: 12px;">' . htmlspecialchars($row['created_at'] ?? '-') . '</span></td>';
                                        echo '</tr>';
                                    }
                                    echo '</tbody></table>';
                                } else {
                                    echo '<div class="text-center py-4"><p class="text-muted mb-0">No providers yet</p></div>';
                                }
                            }
                        } catch (Exception $e) {
                            echo '<p class="text-danger">Error loading data</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .icon-box-primary.sm {
        width: 40px;
        height: 40px;
        border-radius: 10px;
    }

    .col-xl-2-5 {
        width: 20%;
    }

    @media (max-width: 1200px) {
        .col-xl-2-5 {
            width: 33.33%;
        }
    }

    @media (max-width: 768px) {
        .col-xl-2-5 {
            width: 100%;
        }
    }
</style>

<?php
$conn->close();
include 'includes/footer.php';
?>