<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/config.php';

// Check if user is logged in
if (!is_user_logged_in()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $booking_id = intval($_POST['booking_id']);
    $vehicle_id = intval($_POST['vehicle_id']);
    $user_id = intval($_SESSION['user_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    $conn = Connect();
    if (!$conn) {
        die("Database connection failed.");
    }

    // Insert feedback
    $stmt = $conn->prepare("INSERT INTO feedbacks (booking_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $booking_id, $user_id, $rating, $comment);
    $stmt->execute();
    $stmt->close();

    // Update booking to mark feedback as given
    $stmt = $conn->prepare("UPDATE bookings SET feedback_given='yes' WHERE booking_id=?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();

    // Redirect back to My Bookings page with success message
    $_SESSION['message'] = "Thank you for your feedback!";
    $_SESSION['message_type'] = "success";
    header("Location: booking_details.php");
    exit();
}
?>
