<?php
require_once 'includes/config.php';
$conn = Connect();
$tables = ['admins', 'bookings', 'enquiries', 'feedbacks', 'locations', 'payments', 'providers', 'reviews', 'users', 'vehicles'];
foreach ($tables as $table) {
    echo "#### Table: $table\n";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        echo "| Column | Type | Null | Extra |\n";
        echo "| --- | --- | --- | --- |\n";
        while ($row = $result->fetch_assoc()) {
            echo "| {$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Extra']} |\n";
        }
    }
    echo "\n";
}
$conn->close();
?>