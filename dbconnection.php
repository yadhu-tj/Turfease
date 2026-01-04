<?php
$servername = "localhost";
$username = "root";
$password = ""; // Leave blank if no password is set in XAMPP
$dbname = "turfdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
