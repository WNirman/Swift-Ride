<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';

$conn = Connect();

if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the form values
    $vehicle_id = intval($_POST['vehicle_id']);
    $model = $conn->real_escape_string($_POST['vehicle_model']);
    $type = $conn->real_escape_string($_POST['vehicle_type']);
    $price = floatval($_POST['price']);
    $availability = $conn->real_escape_string($_POST['vehicle_availability']);

    // Handle image upload (optional)
    $image_path = null;
    if (isset($_FILES['vehicle_image']) && $_FILES['vehicle_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['vehicle_image']['tmp_name'];
        $fileName = $_FILES['vehicle_image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid('', true) . '.' . $fileExt;

        $uploadDir = __DIR__ . '/../uploads/vehicles/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $image_path = 'uploads/vehicles/' . $newFileName;
        }
    }

    // Build update query
    if ($image_path) {
        $query = "UPDATE Vehicles SET 
                    vehicle_model='$model', 
                    vehicle_type='$type', 
                    price=$price, 
                    vehicle_availability='$availability',
                    vehicle_image='$image_path'
                  WHERE vehicle_id=$vehicle_id AND provider_id=".$_SESSION['provider_id'];
    } else {
        $query = "UPDATE Vehicles SET 
                    vehicle_model='$model', 
                    vehicle_type='$type', 
                    price=$price, 
                    vehicle_availability='$availability'
                  WHERE vehicle_id=$vehicle_id AND provider_id=".$_SESSION['provider_id'];
    }

    if (mysqli_query($conn, $query)) {
        header("Location: add_vehicle.php?updated=1");
        exit();
    } else {
        echo "Error updating vehicle: " . mysqli_error($conn);
    }
} else {
    header("Location: add_vehicle.php");
    exit();
}
?>
