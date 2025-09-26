<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';

// Check if provider is logged in
if (!is_provider_logged_in()) {
    header("Location: provider_login.php");
    exit;
}

$provider_id = $_SESSION['provider_id'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: bookings.php"); // Redirect if no booking ID
    exit;
}

$booking_id = intval($_GET['id']);
$conn = Connect();

// Verify that the booking belongs to one of this provider's vehicles
$sql_check = "
    SELECT b.booking_id
    FROM bookings b
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    WHERE b.booking_id = ? AND v.provider_id = ?
";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $booking_id, $provider_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Booking does not belong to this provider
    header("Location: bookings.php");
    exit;
}

// Update booking status to confirmed
$sql_update = "UPDATE bookings SET status='confirmed' WHERE booking_id=?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    header("Location: bookings.php?success=Booking+approved");
} else {
    header("Location: bookings.php?error=Failed+to+approve+booking");
}

$stmt->close();
$conn->close();
