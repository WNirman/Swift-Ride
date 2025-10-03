<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';
$conn = Connect();

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$days = intval($data['days']);
$people = intval($data['people']);
$city = strtolower(trim($data['city']));

// 1. Find location ID
$stmt = $conn->prepare("SELECT location_id FROM locations WHERE LOWER(city) = ? LIMIT 1");
$stmt->bind_param("s", $city);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) {
    echo json_encode(["reply" => "❌ Sorry, we don’t have a branch in $city. Try Colombo, Jaffna, Kandy..."]);
    exit;
}
$location = $res->fetch_assoc();
$loc_id = $location['location_id'];

// 2. Find available vehicles at this location
$sql = "SELECT * FROM vehicles WHERE location_id = ? AND vehicle_availability='yes' AND seats >= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $loc_id, $people);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["reply" => "😔 No vehicles available for $people people in $city."]);
    exit;
}

// 3. Suggest best vehicles (sorted by price)
$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $cost = $row['price'] * $days;
    $suggestions[] = "{$row['vehicle_brand']} {$row['vehicle_model']} ({$row['seats']} seats) – Rs. $cost";
}

// 4. Travel guide recommendations
$places = [
    "colombo" => ["Galle Face", "Gangaramaya Temple", "Viharamahadevi Park"],
    "kandy" => ["Temple of the Tooth", "Kandy Lake", "Royal Botanical Garden"],
    "jaffna" => ["Nallur Kovil", "Jaffna Fort", "Casuarina Beach"]
];

$placeList = isset($places[$city]) ? implode(", ", $places[$city]) : "local attractions near $city";

// 5. Build reply
$reply = "Here are some options for $people people over $days days from $city:\n\n";
foreach ($suggestions as $s) {
    $reply .= "🚗 $s\n";
}
$reply .= "\n🌍 You can also visit: $placeList.";

echo json_encode(["reply" => nl2br($reply)]);
