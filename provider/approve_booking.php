<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

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

    // --- SEND CONFIRMATION EMAIL USING PHPMailer ---
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
        $mail = new PHPMailer(true);
        try {
            // SMTP server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'wanithnirman@gmail.com';  // Provider's Gmail
            $mail->Password   = 'your_app_password';       // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('wanithnirman@gmail.com', 'Car Rental');
            $mail->addAddress($user['user_email'], $user['user_name']);

            // Email content
            $vehicle_name = trim($user['vehicle_brand'] . ' ' . $user['vehicle_model']);
            $mail->isHTML(true);
            $mail->Subject = "Booking Confirmed for " . $vehicle_name;
            $mail->Body    = "
                <p>Hello {$user['user_name']},</p>
                <p>Your booking for the vehicle <strong>{$vehicle_name}</strong> has been confirmed.</p>
                <p><strong>Booking Details:</strong><br>
                From: {$user['pickup_date']}<br>
                To: {$user['return_date']}</p>
                <p>Thank you for choosing our service!</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            // Log the error but do not block the approval
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    header("Location: bookings.php?success=Booking+approved+and+email+sent");
} else {
    header("Location: bookings.php?error=Failed+to+approve+booking");
}

$stmt_update->close();
$conn->close();
?>
