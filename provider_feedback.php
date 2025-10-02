<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/config.php';

if (!is_provider_logged_in()) {
    header('Location: provider_login.php');
    exit();
}

$provider_id = $_SESSION['provider_id'];
$conn = Connect();

// Fetch feedbacks for provider's vehicles
$stmt = $conn->prepare("
    SELECT f.feedback_id, f.rating, f.comment, f.created_at,
           b.booking_id, u.name AS customer_name,
           v.vehicle_brand, v.vehicle_model
    FROM feedbacks f
    JOIN bookings b ON f.booking_id = b.booking_id
    JOIN users u ON f.user_id = u.user_id
    JOIN vehicles v ON b.vehicle_id = v.vehicle_id
    WHERE v.provider_id = ?
    ORDER BY f.created_at DESC
");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$feedbacks = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Provider Feedbacks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Customer Feedbacks for Your Vehicles</h2>

    <?php if (!empty($feedbacks)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Feedback ID</th>
                        <th>Booking ID</th>
                        <th>Customer Name</th>
                        <th>Vehicle</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feedbacks as $f): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($f['feedback_id']); ?></td>
                            <td><?php echo htmlspecialchars($f['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($f['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($f['vehicle_brand'] . ' ' . $f['vehicle_model']); ?></td>
                            <td><?php echo htmlspecialchars($f['rating']); ?></td>
                            <td><?php echo htmlspecialchars($f['comment']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($f['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No feedback submitted yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
