<?php
require_once '../includes/config.php';

$conn = Connect();

// Default admin password
$default_password = 'admin123';

// Hash it
$hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

// Update admin table
$stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $hashed_password);
$stmt->execute();

echo "Admin password hashed successfully!";
?>
