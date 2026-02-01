<?php
require_once 'includes/config.php';
$conn = Connect();
$result = $conn->query("DESCRIBE Vehicles");
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
?>