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

// Delete the vehicle
$sql_delete = "DELETE FROM vehicles WHERE vehicle_id = ? AND provider_id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("ii", $vehicle_id, $provider_id);

if ($stmt_delete->execute()) {
    // Redirect back to dashboard after successful deletion
    header("Location: dashboard.php?msg=Vehicle+deleted+successfully");
    exit;
} else {
    die("Error: Could not delete vehicle. " . $conn->error);
}
?>
