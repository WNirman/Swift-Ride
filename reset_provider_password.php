<?php
// Include your database connection and auth functions
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Connect to the database
$conn = Connect();

// Provider username to reset
$username = 'provider1';

// Password you want to set
$new_password = '1234';

// Generate a proper bcrypt hash
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the provider's password in the database
$stmt = $conn->prepare("UPDATE providers SET `password` = ? WHERE username = ?");
$stmt->bind_param("ss", $hashed_password, $username);

if ($stmt->execute()) {
    echo "Password for '$username' has been reset successfully! Use password: $new_password";
} else {
    echo "Error updating password: " . $conn->error;
}
?>
