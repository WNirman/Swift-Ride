<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/config.php';

// Ensure provider login
if (!is_provider_logged_in()) {
    header("Location: provider_login.php");
    exit;
}

$provider_id = $_SESSION['provider_id'];
$conn = Connect();
$error = '';

// Handle form submission for adding vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = $conn->real_escape_string($_POST['vehicle_model']);
    $type = $conn->real_escape_string($_POST['vehicle_type']);
    $price = floatval($_POST['price']);
    $availability = $_POST['availability'];

    if (isset($_POST['location_id']) && !empty($_POST['location_id'])) {
        $location_id = intval($_POST['location_id']);
    } else {
        $error = "Please select a vehicle location.";
    }

    if (!$error) {
        // Image upload
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
            } else {
                $error = "Failed to move uploaded file.";
            }
        }

        // Insert into DB
        $stmt = $conn->prepare("
            INSERT INTO Vehicles 
            (provider_id, location_id, vehicle_model, vehicle_type, price, vehicle_availability, vehicle_image)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissdss", $provider_id, $location_id, $model, $type, $price, $availability, $image_path);

        if ($stmt->execute()) {
            // ✅ Redirect back to dashboard with success message
            header("Location: dashboard.php?msg=Vehicle+added+successfully");
            exit;
        } else {
            $error = "Error adding vehicle: " . $conn->error;
        }
    }
}

// Fetch Locations for dropdown
$locations = $conn->query("SELECT location_id, city, branch FROM Locations ORDER BY city ASC");

// Fetch vehicles added by this provider
$vehicles = $conn->query("SELECT v.*, l.city, l.branch 
                          FROM Vehicles v 
                          LEFT JOIN Locations l ON v.location_id = l.location_id
                          WHERE v.provider_id = $provider_id
                          ORDER BY v.vehicle_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Vehicle</title>
    <link href="../style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/yourkitid.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include __DIR__ . '/../includes/provider_navbar.php'; ?>

    <div class="container main-content mt-4">
        <h3 class="mb-4">Add New Vehicle</h3>

        <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <!-- Add Vehicle Form -->
        <div class="card p-4 mb-3">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="vehicle_model" class="form-label">Vehicle Model</label>
                    <input type="text" class="form-control" name="vehicle_model" id="vehicle_model" required>
                </div>

                <div class="mb-3">
                    <label for="vehicle_type" class="form-label">Vehicle Type</label>
                    <select class="form-select" name="vehicle_type" id="vehicle_type" required>
                        <option value="">Select Type</option>
                        <option value="Car">Car</option>
                        <option value="Van">Van</option>
                        <option value="Bike">Bike</option>
                        <option value="Bus">Bus</option>
                        <option value="Bicycle">Bicycle</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="location_id" class="form-label">Vehicle Location</label>
                    <select class="form-select" name="location_id" id="location_id" required>
                        <option value="">Select Location</option>
                        <?php while ($loc = $locations->fetch_assoc()): ?>
                            <option value="<?= $loc['location_id'] ?>"><?= $loc['city'] ?> - <?= $loc['branch'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price / Day ($)</label>
                    <input type="number" step="0.01" class="form-control" name="price" id="price" required>
                </div>

                <div class="mb-3">
                    <label for="availability" class="form-label">Availability</label>
                    <select class="form-select" name="availability" id="availability" required>
                        <option value="yes">Available</option>
                        <option value="no">Not Available</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="vehicle_image" class="form-label">Vehicle Image</label>
                    <input type="file" class="form-control" name="vehicle_image" id="vehicle_image" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Vehicle</button>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </form>
        </div>

        <!-- Vehicle List -->
        <h4>Your Vehicles</h4>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Model</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Price</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($v = $vehicles->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if($v['vehicle_image']): ?>
                            <img src="../<?= $v['vehicle_image'] ?>" width="100">
                        <?php endif; ?>
                    </td>
                    <td><?= $v['vehicle_model'] ?></td>
                    <td><?= $v['vehicle_type'] ?></td>
                    <td><?= $v['city'] ?> - <?= $v['branch'] ?></td>
                    <td>$<?= number_format($v['price'], 2) ?></td>
                    <td><?= ucfirst($v['vehicle_availability']) ?></td>
                    <td>
                        <a href="edit_vehicle.php?id=<?= $v['vehicle_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_vehicle.php?id=<?= $v['vehicle_id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete <?= $v['vehicle_model'] ?>?');">
                           Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
