<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/auth.php';

if (!function_exists('provider_login')) {
    die("DEBUG: provider_login() not found in " . realpath(__DIR__ . '/../includes/auth.php'));
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (provider_login($username, $password)) {
        redirect("dashboard.php"); // ✅ corrected file
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Provider Login</title>
    <link href="../style.css" rel="stylesheet"> <!-- ✅ fixed path -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow" style="width: 400px;">
        <h3 class="mb-3 text-center">Provider Login</h3>
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="post">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
        </form>
    </div>
</body>
</html>
