<?php
require_once 'includes/config.php';
$conn = Connect();
$sql = "ALTER TABLE Vehicles ADD COLUMN IF NOT EXISTS is_deleted TINYINT(1) DEFAULT 0";
if ($conn->query($sql)) {
    echo "Column added or already exists\n";
} else {
    echo "Error: " . $conn->error . "\n";
}
$conn->close();
?>