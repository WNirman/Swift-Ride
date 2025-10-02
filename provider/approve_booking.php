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
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("i", $booking_id);

if ($stmt_update->execute()) {

    // --- SEND CONFIRMATION EMAIL ---
    $sql_user = "
        SELECT u.name AS user_name, u.email AS user_email, v.vehicle_brand, v.vehicle_model, b.pickup_date, b.return_date
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN vehicles v ON b.vehicle_id = v.vehicle_id
        WHERE b.booking_id = ?
    ";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $booking_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user = $result_user->fetch_assoc();

    if ($user) {
        $to = $user['user_email'];
        $vehicle_name = trim($user['vehicle_brand'] . ' ' . $user['vehicle_model']); // Combine brand and model
        $subject = "Booking Confirmed for " . $vehicle_name;
        $message = "Hello " . $user['user_name'] . ",\n\n";
        $message .= "Your booking for the vehicle '" . $vehicle_name . "' has been confirmed.\n\n";
        $message .= "Booking Details:\n";
        $message .= "- From: " . $user['pickup_date'] . "\n";
        $message .= "- To: " . $user['return_date'] . "\n\n";
        $message .= "Thank you for choosing our service!\n";

        $headers = "From: no-reply@carrental.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        mail($to, $subject, $message, $headers);
    }

    header("Location: bookings.php?success=Booking+approved+and+email+sent");
} else {
    header("Location: bookings.php?error=Failed+to+approve+booking");
}

$stmt_update->close();
$conn->close();
?>