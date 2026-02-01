<?php
require_once 'includes/config.php';
$conn = Connect();
$tables = ['Users', 'Vehicles', 'Bookings', 'Providers', 'Locations', 'Feedback', 'Enquiries'];
foreach ($tables as $table) {
    echo "### Table: $table\n";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']} ({$row['Type']})\n";
        }
    }
    echo "\n";
}
$conn->close();
?>