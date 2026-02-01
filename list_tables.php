<?php
require_once 'includes/config.php';
$conn = Connect();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    echo "Table: {$row[0]}\n";
}
$conn->close();
?>