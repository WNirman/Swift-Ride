<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';

// Ensure provider is logged in
if (!is_provider_logged_in()) {
    header("Location: provider_login.php");
    exit;
}

$provider_id = $_SESSION['provider_id'];

// Check if 'id' is passed in URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Vehicle ID is missing.");
}

$vehicle_id = intval($_GET['id']);

// Connect to database
$conn = Connect();

// Optional: Verify that this vehicle belongs to this provider
$sql_check = "SELECT * FROM vehicles WHERE vehicle_id = ? AND provider_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $vehicle_id, $provider_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    die("Error: Vehicle not found or you don't have permission to delete it.");
}

// Check if there are existing bookings for this vehicle
$sql_booking = "SELECT COUNT(*) as count FROM bookings WHERE vehicle_id = ?";
$stmt_booking = $conn->prepare($sql_booking);
$stmt_booking->bind_param("i", $vehicle_id);
$stmt_booking->execute();
$result_booking = $stmt_booking->get_result();
$row_booking = $result_booking->fetch_assoc();

// Perform Soft Delete: Set is_deleted = 1
$sql_delete = "UPDATE vehicles SET is_deleted = 1, vehicle_availability = 'no' WHERE vehicle_id = ? AND provider_id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("ii", $vehicle_id, $provider_id);

if ($stmt_delete->execute()) {
    $msg = ($row_booking['count'] > 0)
        ? "Vehicle archived successfully (permanent records kept due to existing bookings)"
        : "Vehicle removed successfully";
    header("Location: dashboard.php?msg=" . urlencode($msg));
    exit;
} else {
    die("Error: Could not delete vehicle. " . $conn->error);
}
?>