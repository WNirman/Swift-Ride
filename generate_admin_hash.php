<?php
// Replace 'admin123' with the password you want for the default admin
$hash = password_hash('admin123', PASSWORD_DEFAULT);
echo $hash;
?>
