<?php
// submit_feedback.php
session_start();
require_once 'includes/functions.php'; // must define Connect()
$conn = Connect();
if (!$conn) { die('DB connection error'); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: booking_details.php');
    exit;
}

$booking_id = intval($_POST['booking_id'] ?? 0);
$vehicle_id = intval($_POST['vehicle_id'] ?? 0);
$rating = intval($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$user_id = intval($_SESSION['user_id'] ?? 0);

// basic validation
if ($booking_id <= 0 || $vehicle_id <= 0 || $rating < 1 || $rating > 5 || $user_id <= 0) {
    $_SESSION['feedback_error'] = "Invalid feedback submission.";
    header('Location: booking_details.php');
    exit;
}

// verify booking belongs to user & is eligible
$stmt = $conn->prepare("SELECT booking_id, user_id, vehicle_id, status, return_date, feedback_given FROM bookings WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    $_SESSION['feedback_error'] = "Booking not found.";
    header('Location: booking_details.php');
    exit;
}
$row = $res->fetch_assoc();
if (intval($row['user_id']) !== $user_id || intval($row['vehicle_id']) !== $vehicle_id) {
    $_SESSION['feedback_error'] = "This booking isn't yours.";
    header('Location: booking_details.php');
    exit;
}

// ensure trip is finished and not already given
$today = date('Y-m-d');
if (!in_array($row['status'], ['confirmed','completed'])) {
    $_SESSION['feedback_error'] = "Booking not eligible for feedback (status).";
    header('Location: booking_details.php');
    exit;
}
if ($row['return_date'] >= $today) {
    $_SESSION['feedback_error'] = "Return date not passed yet.";
    header('Location: booking_details.php');
    exit;
}
if ($row['feedback_given'] === 'yes') {
    $_SESSION['feedback_error'] = "Feedback already given for this booking.";
    header('Location: booking_details.php');
    exit;
}

// insert review
$stmt = $conn->prepare("INSERT INTO reviews (booking_id, vehicle_id, user_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiis", $booking_id, $vehicle_id, $user_id, $rating, $comment);
$ok = $stmt->execute();
if (!$ok) {
    $_SESSION['feedback_error'] = "Failed to save review: " . $conn->error;
    header('Location: booking_details.php');
    exit;
}

// mark booking feedback_given = 'yes'
$stmt = $conn->prepare("UPDATE bookings SET feedback_given = 'yes' WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();

// recompute avg rating for vehicle and write to vehicles.avg_rating
$stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM reviews WHERE vehicle_id = ?");
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$res = $stmt->get_result();
$avg = $res->fetch_assoc()['avg_rating'] ?? 0;
$avg = round(floatval($avg), 2);

$stmt = $conn->prepare("UPDATE vehicles SET avg_rating = ? WHERE vehicle_id = ?");
$stmt->bind_param("di", $avg, $vehicle_id);
$stmt->execute();

// notify provider by email (if email exists)
$stmt = $conn->prepare("SELECT p.email, p.name FROM providers p JOIN vehicles v ON p.provider_id = v.provider_id WHERE v.vehicle_id = ?");
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$prov = $stmt->get_result()->fetch_assoc();
if ($prov && !empty($prov['email'])) {
    $to = $prov['email'];
    $subject = "New Feedback for Your Vehicle (Booking #$booking_id)";
    $body = "Hello " . ($prov['name'] ?? '') . ",\n\nA customer left a rating of $rating for vehicle ID $vehicle_id.\n\nComment:\n$comment\n\nAverage rating is now: $avg\n\nRegards,\nCar Rental Platform";
    // NOTE: mail() may not work on local dev without config; use it if your server supports it.
    @mail($to, $subject, $body);
}

// redirect back with success
$_SESSION['feedback_success'] = "Thank you — your review was submitted.";
header('Location: booking_details.php');
exit;
