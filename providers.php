<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';


if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}

$conn = Connect();
$page_title = 'Providers';

include 'includes/header.php';
?>

<div class="container py-4">
    <h1>All Providers</h1>
    <?php
    $result = $conn->query("SELECT * FROM providers ORDER BY created_at DESC");
    if ($result && $result->num_rows > 0) {
        echo '<table class="table">';
        echo '<thead><tr><th>Name</th><th>Email</th><th>Phone</th></tr></thead><tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
            echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No providers found.</p>';
    }
    ?>
</div>

<?php include 'includes/footer.php'; $conn->close(); ?>
