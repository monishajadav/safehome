<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "safehome_db";
$port       = 3307;  // 👈 Add this

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// Check connection
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
?>