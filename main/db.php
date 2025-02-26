<?php
$servername = "192.168.0.200";
$username = "root";
$password = "seventh7seal";
$dbname = "UserProfileDB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
