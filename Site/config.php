<?php
$servername = "localhost";
$username = "root"; // default username for XAMPP
$password = ""; // default password is empty in XAMPP
$dbname = "CookingSite";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
