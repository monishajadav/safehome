<?php
include('includes/db_connect.php');

// Set new password here
$new_password = "admin123";
$admin_username = "admin";

// Hash the password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the admin password
$sql = "UPDATE admin SET password='$hashed_password' WHERE username='$admin_username'";

if(mysqli_query($conn, $sql)) {
    echo "<h2>Password Reset Successful!</h2>";
    echo "<p><strong>Username:</strong> $admin_username</p>";
    echo "<p><strong>New Password:</strong> $new_password</p>";
    echo "<br><a href='admin/login.php'>Go to Admin Login</a>";
    echo "<br><br><strong style='color:red;'>DELETE THIS FILE (reset_admin.php) NOW!</strong>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>